<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix'=>'v1', 'namespace'=>'v1\Back'], function (){
    Route::post('/login', 'LoginController@login');
    Route::get('/test', 'LoginController@test');
    Route::get('/test2', 'LoginController@test2');
//    Route::get('/xindao/show/{channel_id}/{status}','ChannelController@showDetail');
/** ===================== ========== 微信小程序 =========== =========================*/
    Route::post('/findUser', 'LoginController@findUser'); //验证是否注册过
    Route::get('/findUserViaOpenid/{openid}', 'LoginController@findUserViaOpenid'); //通过openid直接校验jwt
    Route::get('/getjwt',  'LoginController@getJWT');
    Route::post('/emp_waitings','EmpWaitingController@store');  //微信小程序申请注册
    Route::get('/emp_waitings/delete/{id}','EmpWaitingController@delete');  //微信小程序申请注册 => 删除之前被拒绝的申请信息
    Route::get('/emp_waitings/sendMsg/{phoneNumber}','EmpWaitingController@sendMsg');  //微信小程序申请注册 => 生成验证码并发送
    Route::get('/emp_waitings/checkCode/{code}','EmpWaitingController@checkCode');  //微信小程序申请注册 => 短信验证
    Route::get('/emp_waitings/search/{openid}','EmpWaitingController@search');  //微信小程序申请注册 => 查询注册进度

    Route::post('/upload/{service_id}', 'SP\commonController@upload');

    //需要令牌组
    Route::group(['middleware'=>['jwt.empAuth', 'jwt.refreshEmp'], 'namespace'=>'SP'], function (){
        //todo 检索分组
        Route::get('/searchCompany/{keyword}','commonController@searchCompany');  //查询单位
        Route::get('/searchCompanyByNo/{number}','commonController@searchCompanyByNo');  //查询单位
        Route::get('/searchContract/{company_id}','commonController@searchContract');  //查询合同
        Route::get('/searchServiceType','commonController@searchServiceType');  //查询服务类型
        Route::post('/searchMeal','commonController@searchMeal');  //查询可用服务类型 + 套餐详情

        //todo 小程序派单模块
        Route::group(['prefix'=>'paidan'], function (){
            Route::get('/page/{page}/{pageSize}/{emp_id}/{status}', 'JobController@showServiceList'); //检索与自己有关的服务单
            Route::get('/askFinish/{serviceid}', 'JobController@askFinish'); //检索与自己有关的服务单
            Route::get('/s/{name}/{page}/{pageSize}','JobController@search');  //todo 模糊搜索
            Route::get('/{service_id}','JobController@showServiceDetail');  //todo 服务详情
            Route::post('/getServiceInfo', 'JobController@getServiceInfo'); //todo 得到服务单详情以确保搜索人能不能查看详情
        });

        //todo 报修
        Route::group(['prefix'=>'repair'], function (){
            Route::post('/apply','repairController@apply');
            Route::get('/getProcess/{page}/{pageSize}/{emp_id}/{status}','repairController@getProcess');
            Route::post('/allege/{service_id}','repairController@allege');
        });

        //todo 信道
        Route::group(['prefix'=>'SP/channel'], function (){
            Route::get('/xindao/{page}/{pageSize}/{emp_id}/{status}','ChannelController@page');
            Route::get('/xindao/show/{channel_id}/{status}','ChannelController@showDetail');
            Route::post('/xindao/getChannelInfo','ChannelController@getChannelInfo');   //todo 得到服务单详情以确保搜索人能不能查看详情
            Route::get('/xindao/searchContractc/{company_id}','ChannelController@searchContractc');
            Route::get('/xindao/searchPlan/{contractc_id}','ChannelController@searchPlan');
            Route::get('/xindao/searchDevice/{company_id}','ChannelController@searchDevice');
            Route::post('/xindao/apply','ChannelController@apply');
            Route::post('/xindao/allege','ChannelController@allege');
        });

    });
/** ===================== ========== FileUpload =========== =========================*/
//    //todo 公用上传/临时删除file
    Route::post('upload', 'ContractController@uploadFileToTemp');
    Route::post('deleteFile','ContractController@deleteFile');
/** ===================== ========== JWT-AUTH =========== =========================*/
    Route::group(['middleware'=>['jwt.auth', 'jwt.refresh']], function (){
        //todo checkJWT
        Route::get('/check', 'LoginController@check');

        //todo Users
        Route::group(['prefix'=>'users'], function () {
            Route::get('/page/{page}/{pageSize}', 'UserController@page');
            Route::resource('/', 'UserController');
        });

        /** company
         *  @url api/v1/company
         */
        Route::group(['prefix'=>'company'], function (){
            Route::post('/page/{page}/{pageSize}','CompanyController@page');
            Route::get('/s/{name}/{page}/{pageSize}','CompanyController@search');  //todo 模糊搜索

            //todo 得到某个单独emp的数据
            Route::get('/emps/{id}', 'CompanyController@showEmps');
            Route::get('/contracts/{id}', 'CompanyController@showContracts');
            Route::get('/services/{id}', 'CompanyController@showServices');
            Route::get('/channels/{id}', 'CompanyController@showChannels');
            Route::post('/update/{id}', 'CompanyController@update');
            Route::get('/delete/{id}', 'CompanyController@destroy');
            Route::resource('/','CompanyController');
        });

        //todo emp
        Route::group(['prefix'=>'employees'], function(){
            Route::post('/page/{page}/{pageSize}', 'EmployeeController@page');
            Route::get('/s/{name}/{page}/{pageSize}','EmployeeController@search');  //todo 模糊搜索
            Route::get('/sc/{companyName}','EmployeeController@searchCompanies');  //todo ES模糊搜索单位
            Route::get('/se/out/{empName}','EmployeeController@searchOutEmps');  //todo 模糊搜索外部员工
            Route::get('/se/inner/{empName}','EmployeeController@searchInnerEmps');  //todo 模糊搜索中网员工
            Route::get('/scon/{contract_id}','EmployeeController@searchContracts');  //todo 模糊搜索普通合同编号
            Route::get('/sconc/{contract_id}','EmployeeController@searchContractcs');  //todo 模糊搜索信道合同编号
            Route::get('/sd/{company_id}','EmployeeController@searchDevices');  //todo 准确搜索单位id下的设备(节点) -- 只能是未损坏的
            Route::post('/update/{id}', 'EmployeeController@update');
            Route::get('/delete/{id}','EmployeeController@destroy');
            Route::get('/verify', 'EmployeeController@verify');  //todo 筛选未审核者
            Route::post('/pass/{id}/{contract_plan_id}', 'EmployeeController@pass');  //todo 通过未审核者
            Route::post('/rej/{id}', 'EmployeeController@rej');  //todo 拒绝未审核者
            Route::resource('/','EmployeeController');
        });

        //todo emp_wating
        Route::group(['prefix'=>'emp_watings'], function(){
            Route::get('/page/{page}/{pageSize}', 'EmpWaitingController@page');
            Route::get('/s/{name}/{page}/{pageSize}','EmpWaitingController@search');  //todo 模糊搜索人员
            Route::post('/update/{id}', 'EmpWaitingController@update');
            Route::get('/delete/{id}','EmpWaitingController@destroy');
//            Route::post('/','EmployeeController@store');  //申请注册, 被提到jwt外面去了
        });

        //todo contracts
        Route::group(['prefix'=>'contracts'], function(){
            Route::post('/newpage', 'ContractController@newPage');
            Route::post('/page/{page}/{pageSize}', 'ContractController@page');
            Route::get('/s/{name}/{page}/{pageSize}','ContractController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'ContractController@update');
            Route::get('/delete/{id}','ContractController@destroy');
            Route::post('/updateMoney/{contract_id}', 'ContractController@updateMoney');  //回款详情更新/创建
            Route::post('/createMoneyDetail/{contract_id}', 'ContractController@createMoneyDetail');  //历次回款记录--创建
            Route::get('/delMoneyDetail/{money_detail_id}', 'ContractController@delMoneyDetail');       //历次回款记录--删除
            Route::get('/getContractPlans/{contract_id}', 'ContractController@getContractPlans');  //todo 检索合同下的套餐
            Route::post('/addPlan/{contract_id}', 'ContractController@addPlan');       //todo 新增套餐
            Route::get('/deletePlan/{id}', 'ContractController@deletePlan');       //todo 删除套餐
            Route::resource('/','ContractController');
        });

        //todo services
        Route::group(['prefix'=>'services'], function(){
            Route::post('/page/{page}/{pageSize}', 'ServiceController@page');
            Route::post('/update/{id}', 'ServiceController@update');
            Route::get('/delete/{id}','ServiceController@destroy');
            Route::get('/verify/{status}', 'ServiceController@verify');  //todo 筛选未审核服务单
            Route::get('/temp/verify', 'ServiceController@verifyTemp');  //todo 筛选临时未审核服务单
            Route::post('/pass', 'ServiceController@pass');  //todo 通过未审核服务单
            Route::get('/rej/{id}', 'ServiceController@rej');  //todo 拒绝未审核服务单
            Route::get('/passFinish/{service_id}', 'ServiceController@passFinish');  //todo 通过服务单完成申请
            Route::get('/rejectFinish/{service_id}', 'ServiceController@rejectFinish');  //todo 拒绝服务单完成申请
            Route::resource('/','ServiceController');

            /** 回访 */
            Route::post('/visit/{id}', 'ServiceController@visit');
        });

        //todo contract_cs
        Route::group(['prefix'=>'contractcs'], function(){
            Route::post('/page/{page}/{pageSize}', 'ContractcController@page');
            Route::get('/s/{name}/{page}/{pageSize}','ContractcController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'ContractcController@update');
            Route::get('/delete/{id}','ContractcController@destroy');
            Route::post('/updateMoney/{contractc_id}', 'ContractcController@updateMoney');  //回款详情更新/创建
            Route::post('/createMoneyDetail/{contractc_id}', 'ContractcController@createMoneyDetail');  //历次回款记录--创建
            Route::get('/delMoneyDetail/{money_detail_id}', 'ContractcController@delMoneyDetail');       //历次回款记录--删除
            Route::get('/getContractcPlans/{contractc_id}', 'ContractcController@getContractcPlans');
            Route::post('/addPlan/{contractc_id}', 'ContractcController@addPlan');       //todo 新增套餐
            Route::get('/deletePlan/{contractc_id}', 'ContractcController@deletePlan');       //todo 删除套餐
            Route::resource('/','ContractcController');
        });

        //todo channels
        Route::group(['prefix'=>'channels'], function(){
            Route::post('/page/{page}/{pageSize}', 'ChannelController@page');
            Route::get('/s/{name}/{page}/{pageSize}','ChannelController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'ChannelController@update');
            Route::get('/delete/{id}','ChannelController@destroy');
            Route::resource('/','ChannelController');
        });
        Route::post('channel_apply/deleteRelation', 'Channels\RelationController@deleteRelation');
        Route::post('channel_apply/addDeviceToChannel', 'Channels\RelationController@addDeviceToChannel');

        //todo 信道申请
        Route::group(['prefix'=>'apply', 'namespace'=>'Channels'], function(){
            Route::post('/page/{page}/{pageSize}', 'ApplyController@page');  //筛选信道单
            Route::post('/update/{id}', 'ApplyController@update');  //修改 + 审核通过
            Route::post('/operative', 'ApplyController@updateOperative');  //更新运行调配表
            Route::post('/real', 'ApplyController@updateReal');  //更新实际表
            Route::get('/rej/{id}','ApplyController@rej');  //拒绝
            Route::resource('/', 'ApplyController');
        });

        Route::post('applyTemp/page/{page}/{pageSize}', 'Channels\ApplyController@pageTemp');  //todo 筛选走临时合同的信道单
        /***************************************************************************************************
        ***************                          工具类                                     ****************
        ***************************************************************************************************/

        //todo coors
        Route::group(['prefix'=>'coors', 'namespace'=>'Utils'], function (){
            Route::get('/page/{page}/{pageSize}', 'CoorController@page');
            Route::get('/s/{name}/{page}/{pageSize}','CoorController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'CoorController@update');
            Route::get('/delete/{id}','CoorController@destroy');
            Route::resource('/','CoorController');
        });

        //todo contractType
        Route::group(['prefix'=>'contractTypes', 'namespace'=>'Utils'], function (){
            Route::get('/page/{page}/{pageSize}', 'ContractTypeController@page');
            Route::get('/s/{name}/{page}/{pageSize}','ContractTypeController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'ContractTypeController@update');
            Route::get('/delete/{id}','ContractTypeController@destroy');
            Route::resource('/','ContractTypeController');
        });

        //todo serviceType
        Route::group(['prefix'=>'serviceTypes', 'namespace'=>'Utils'], function (){
            Route::get('/page/{page}/{pageSize}', 'ServiceTypeController@page');
            Route::get('/s/{name}/{page}/{pageSize}','ServiceTypeController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'ServiceTypeController@update');
            Route::get('/delete/{id}','ServiceTypeController@destroy');
            Route::resource('/','ServiceTypeController');
        });

        //todo serviceSource
        Route::group(['prefix'=>'serviceSources', 'namespace'=>'Utils'], function (){
            Route::get('/page/{page}/{pageSize}', 'ServiceSourceController@page');
            Route::get('/s/{name}/{page}/{pageSize}','ServiceSourceController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'ServiceSourceController@update');
            Route::get('/delete/{id}','ServiceSourceController@destroy');
            Route::resource('/','ServiceSourceController');
        });

        //todo profession 行业
        Route::group(['prefix'=>'pros', 'namespace'=>'Utils'], function (){
            Route::get('/page/{page}/{pageSize}', 'ProfessionController@page');
            Route::get('/s/{name}/{page}/{pageSize}','ProfessionController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'ProfessionController@update');
            Route::get('/delete/{id}','ProfessionController@destroy');
            Route::resource('/','ProfessionController');
        });

        /**
         * File
         * file没有add,   file只在各个环境下add,  本处只做展示, 检索, 修删
         */
        Route::group(['prefix'=>'files', 'namespace'=>'Utils'], function (){
            Route::get('/page/{page}/{pageSize}', 'FileController@page');
            Route::get('/s/{name}/{page}/{pageSize}','FileController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'FileController@update');
            Route::get('/delete/{id}','FileController@destroy');
            Route::resource('/','FileController');
        });

        //todo 带宽表 info1
        Route::group(['prefix'=>'info1', 'namespace'=>'Utils'], function (){
            Route::get('/page/{page}/{pageSize}', 'Info1Controller@page');
            Route::get('/s/{name}/{page}/{pageSize}','Info1Controller@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'Info1Controller@update');
            Route::get('/delete/{id}','Info1Controller@destroy');
            Route::resource('/','Info1Controller');
        });

        //todo 站类型表 info2
        Route::group(['prefix'=>'info2', 'namespace'=>'Utils'], function (){
            Route::get('/page/{page}/{pageSize}', 'Info2Controller@page');
            Route::get('/s/{name}/{page}/{pageSize}','Info2Controller@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'Info2Controller@update');
            Route::get('/delete/{id}','Info2Controller@destroy');
            Route::resource('/','Info2Controller');
        });

        //todo 通信卫星表 info3
        Route::group(['prefix'=>'info3', 'namespace'=>'Utils'], function (){
            Route::get('/page/{page}/{pageSize}', 'Info3Controller@page');
            Route::get('/s/{name}/{page}/{pageSize}','Info3Controller@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'Info3Controller@update');
            Route::get('/delete/{id}','Info3Controller@destroy');
            Route::resource('/','Info3Controller');
        });

        //todo 频率表 info4
        Route::group(['prefix'=>'info4', 'namespace'=>'Utils'], function (){
            Route::get('/page/{page}/{pageSize}', 'Info4Controller@page');
            Route::get('/s/{name}/{page}/{pageSize}','Info4Controller@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'Info4Controller@update');
            Route::get('/delete/{id}','Info4Controller@destroy');
            Route::resource('/','Info4Controller');
        });

        //todo 极化表 info5
        Route::group(['prefix'=>'info5', 'namespace'=>'Utils'], function (){
            Route::get('/page/{page}/{pageSize}', 'Info5Controller@page');
            Route::get('/s/{name}/{page}/{pageSize}','Info5Controller@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'Info5Controller@update');
            Route::get('/delete/{id}','Info5Controller@destroy');
            Route::resource('/','Info5Controller');
        });

        //todo 网络类型表 info6
        Route::group(['prefix'=>'info6', 'namespace'=>'Utils'], function (){
            Route::get('/page/{page}/{pageSize}', 'Info6Controller@page');
            Route::get('/s/{name}/{page}/{pageSize}','Info6Controller@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'Info6Controller@update');
            Route::get('/delete/{id}','Info6Controller@destroy');
            Route::resource('/','Info6Controller');
        });

        //todo 设备
        Route::group(['prefix'=>'devices', 'namespace'=>'Utils'], function (){
            Route::post('/page/{page}/{pageSize}', 'DeviceController@page');
            Route::get('/s/{name}/{page}/{pageSize}','DeviceController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'DeviceController@update');
            Route::get('/delete/{id}','DeviceController@destroy');
            Route::post('/report','DeviceController@report');
            Route::resource('/','DeviceController');
        });

        //todo 信道套餐
        Route::group(['prefix'=>'plans', 'namespace'=>'Utils'], function (){
            Route::get('/page/{page}/{pageSize}', 'PlanController@page');
            Route::get('/s/{name}/{page}/{pageSize}','PlanController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'PlanController@update');
            Route::get('/delete/{id}','PlanController@destroy');
            Route::resource('/','PlanController');
        });

        //todo 统计
        Route::group(['prefix'=>'calculate'], function (){
            Route::get('/basic', 'CalculateController@basic');
            Route::get('/userInfo', 'CalculateController@userInfo');
        });

        //todo 信道值班表
        Route::group(['prefix'=>'channelduty', 'namespace'=>'Channels'], function (){
            Route::get('/page/{page}/{pageSize}', 'DutyController@page');
            Route::get('/s/{name}/{page}/{pageSize}','DutyController@search');  //todo 模糊搜索名字
//            Route::get('/s/{searchDate}/searchDate/{page}/{pageSize}','DutyController@searchDate');  //todo 搜索日期
            Route::post('/update/{id}', 'DutyController@update');
            Route::get('/delete/{id}','DutyController@destroy');
            Route::resource('/','DutyController');
        });

        //todo 普通服务套餐列表
        Route::group(['prefix'=>'contract_plans', 'namespace'=>'Contracts'], function (){
            Route::get('/page/{page}/{pageSize}', 'ContractPlanController@page');
            Route::get('/s/{name}/{page}/{pageSize}','ContractPlanController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'ContractPlanController@update');
            Route::get('/delete/{id}','ContractPlanController@destroy');
            Route::resource('/','ContractPlanController');
        });

        /**
         * ----------------------故障信息库--------------------------
         */
        Route::group(['prefix'=>'problem', 'namespace'=>'Problems'], function (){
            Route::post('/page/{page}/{pageSize}', 'ProblemController@getPage');
            Route::post('/report','ProblemController@report');
            Route::get('/s/{query}','ProblemController@searchProblem');
            Route::post('/update/{problem_id}', 'ProblemController@update');
            Route::get('/delete/{problem_id}','ProblemController@delete');
            Route::resource('/','ProblemController');
        });

        Route::group(['prefix'=>'ptype', 'namespace'=>'Problems'], function (){
            Route::post('/page/{page}/{pageSize}', 'ProblemTypeController@getPage');
//            Route::get('/s/{name}/{page}/{pageSize}','ProblemTypeController@search');  //todo 模糊搜索
            Route::post('/update/{ptype_id}', 'ProblemTypeController@update');
            Route::get('/delete/{ptype_id}','ProblemTypeController@delete');
            Route::resource('/','ProblemTypeController');
        });

        Route::group(['prefix'=>'precord', 'namespace'=>'Problems'], function (){
            Route::post('/page/{page}/{pageSize}', 'ProblemRecordController@getPage');
//            Route::get('/s/{name}/{page}/{pageSize}','ProblemController@search');  //todo 模糊搜索
//            Route::post('/update/{precord_id}', 'ProblemRecordController@update');
            Route::get('/delete/{precord_id}','ProblemRecordController@delete');
            Route::resource('/','ProblemController');
        });

        Route::group(['prefix'=>'allow', 'namespace'=>'Utils'], function (){
            Route::get('/report/{allow_int}', 'AllowController@report');
        });
    });
});
