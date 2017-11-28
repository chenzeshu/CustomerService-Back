<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix'=>'v1', 'namespace'=>'v1\Back'], function (){
    Route::post('/login', 'LoginController@login');
    Route::get('/test', 'LoginController@test');
    Route::get('/test2', 'LoginController@test2');
/** ===================== ========== JWT-AUTH =========== =========================*/
    Route::group(['middleware'=>['jwt.auth', 'jwt.refresh']], function (){
//    Route::group(['middleware'=>[]], function (){
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
            Route::get('/page/{page}/{pageSize}','CompanyController@page');
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
            Route::get('/page/{page}/{pageSize}', 'EmployeeController@page');
            Route::get('/s/{name}/{page}/{pageSize}','EmployeeController@search');  //todo 模糊搜索
            Route::get('/sc/{companyName}','EmployeeController@searchCompanies');  //todo 模糊搜索单位
            Route::get('/se/out/{empName}','EmployeeController@searchOutEmps');  //todo 模糊搜索外部员工
            Route::get('/se/inner/{empName}','EmployeeController@searchInnerEmps');  //todo 模糊搜索中网员工
            Route::get('/scon/{contract_id}','EmployeeController@searchContracts');  //todo 模糊搜索普通合同编号
            Route::get('/sconc/{contract_id}','EmployeeController@searchContractcs');  //todo 模糊搜索信道合同编号
            Route::get('/sd/{company_id}','EmployeeController@searchDevices');  //todo 准确搜索单位id下的设备
            Route::post('/update/{id}', 'EmployeeController@update');
            Route::get('/delete/{id}','EmployeeController@destroy');
            Route::get('/verify', 'EmployeeController@verify');  //todo 筛选未审核者
            Route::get('/pass/{id}', 'EmployeeController@pass');  //todo 通过未审核者
            Route::get('/rej/{id}', 'EmployeeController@rej');  //todo 拒绝未审核者
            Route::resource('/','EmployeeController');
        });

        //todo contracts
        Route::group(['prefix'=>'contracts'], function(){
            Route::get('/page/{page}/{pageSize}', 'ContractController@page');
            Route::get('/s/{name}/{page}/{pageSize}','ContractController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'ContractController@update');
            Route::get('/delete/{id}','ContractController@destroy');
            Route::resource('/','ContractController');
        });

        //todo services
        Route::group(['prefix'=>'services'], function(){
            Route::get('/page/{page}/{pageSize}', 'ServiceController@page');
            Route::get('/s/{name}/{page}/{pageSize}','ServiceController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'ServiceController@update');
            Route::get('/delete/{id}','ServiceController@destroy');
            Route::get('/verify', 'ServiceController@verify');  //todo 筛选未审核者
            Route::get('/pass/{id}', 'ServiceController@pass');  //todo 通过未审核者
            Route::get('/rej/{id}', 'ServiceController@rej');  //todo 拒绝未审核者
            Route::resource('/','ServiceController');

            /** 回访 */
            Route::post('/visit/{id}', 'ServiceController@visit');
        });

        //todo contract_cs
        Route::group(['prefix'=>'contractcs'], function(){
            Route::get('/page/{page}/{pageSize}', 'ContractcController@page');
            Route::get('/s/{name}/{page}/{pageSize}','ContractcController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'ContractcController@update');
            Route::get('/delete/{id}','ContractcController@destroy');
            Route::resource('/','ContractcController');
        });

        //todo channels
        Route::group(['prefix'=>'channels'], function(){
            Route::get('/page/{page}/{pageSize}', 'ChannelController@page');
            Route::get('/s/{name}/{page}/{pageSize}','ChannelController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'ChannelController@update');
            Route::get('/delete/{id}','ChannelController@destroy');
            Route::resource('/','ChannelController');
        });
        Route::post('channel_apply/deleteRelation', 'Channels\RelationController@deleteRelation');
        Route::post('channel_apply/addDeviceToChannel', 'Channels\RelationController@addDeviceToChannel');

        //todo 信道申请
        Route::group(['prefix'=>'apply', 'namespace'=>'Channels'], function(){
            Route::get('/page/{page}/{pageSize}', 'ApplyController@page');
            Route::post('/update/{id}', 'ApplyController@update');  //修改 + 审核通过
            Route::post('/operative/{id}', 'ApplyController@updateOperative');  //更新运行调配表
            Route::post('/real/{id}', 'ApplyController@updateReal');  //更新实际表
            Route::get('/rej/{id}','ApplyController@rej');  //拒绝
            Route::resource('/', 'ApplyController');
        });
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
            Route::get('/page/{page}/{pageSize}', 'DeviceController@page');
            Route::get('/s/{name}/{page}/{pageSize}','DeviceController@search');  //todo 模糊搜索
            Route::post('/update/{id}', 'DeviceController@update');
            Route::get('/delete/{id}','DeviceController@destroy');
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

    });
});
