<?php

use Illuminate\Http\Request;
use Faker\Generator as Faker;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix'=>'v1', 'namespace'=>'v1\Back'], function (){

    Route::post('/login', 'LoginController@login');
    Route::get('/test', 'LoginController@test');

//    Route::group(['prefix'=>'employees'], function(){
//        Route::get('/page/{page}/{pageSize}', 'EmployeeController@page');
//        Route::get('/s/{name}/{page}/{pageSize}','EmployeeController@search');  //todo 模糊搜索
//        Route::get('/sc/{companyName}','EmployeeController@searchCompanies');  //todo 模糊搜索单位
//        Route::get('/se/{empName}','EmployeeController@searchEmps');  //todo 模糊搜索员工
//        Route::post('/update/{id}', 'EmployeeController@update');
//        Route::get('/delete/{id}','EmployeeController@destroy');
//        Route::resource('/','EmployeeController');
//    });

    //todo contracts
    Route::group(['prefix'=>'contracts'], function(){
        Route::get('/page/{page}/{pageSize}', 'ContractController@page');
        Route::get('/s/{name}/{page}/{pageSize}','ContractController@search');  //todo 模糊搜索
        Route::post('/update/{id}', 'ContractController@update');
        Route::get('/delete/{id}','ContractController@destroy');
        Route::resource('/','ContractController');
    });


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
            Route::get('/se/{empName}','EmployeeController@searchEmps');  //todo 模糊搜索员工
            Route::get('/scon/{contract_id}','EmployeeController@searchContracts');  //todo 模糊搜索普通合同编号
            Route::post('/update/{id}', 'EmployeeController@update');
            Route::get('/delete/{id}','EmployeeController@destroy');
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
            Route::resource('/','ServiceController');
        });

        //todo contract_cs
        Route::group(['prefix'=>'Contractcs'], function(){
            Route::resource('/','ContractcController');
        });

        //todo channels
        Route::group(['prefix'=>'channels'], function(){
            Route::resource('/','ChannelController');
        });

        //todo utils
        Route::group(['prefix'=>'utils', 'namespace'=>'Utils'], function (){
            Route::resource('pros', 'ProfessionController');
        });
        //todo
    });
});
