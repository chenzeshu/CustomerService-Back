<?php

use Illuminate\Http\Request;
use Faker\Generator as Faker;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix'=>'v1', 'namespace'=>'v1\Back'], function (){
    Route::get('/test', function (Faker $faker){

    });

    Route::post('/login', 'LoginController@login');
    //todo 开发完后将需要jwt验证的模块放入下方;
/** ===================== ========== JWT-AUTH =========== =========================*/
    Route::group(['middleware'=>['jwt.auth', 'jwt.refresh']], function (){
        //todo checkJWT
            Route::get('/check', 'LoginController@check');



//todo Users
        Route::group(['prefix'=>'users'], function () {
            Route::get('/page/{page}/{pageSize}', 'UserController@page');
            Route::resource('/', 'UserController');
        });

        //todo company
        Route::group(['prefix'=>'company'], function (){
            Route::get('/page/{page}/{pageSize}','CompanyController@page');

            //todo 得到某个单独的数据
            Route::get('/emps/{id}', 'CompanyController@showEmps');
            Route::get('/contracts/{id}', 'CompanyController@showContracts');
            Route::get('/services/{id}', 'CompanyController@showServices');
            Route::get('/channels/{id}', 'CompanyController@showChannels');

            Route::resource('/','CompanyController');
        });

        //todo emp
        Route::group(['prefix'=>'employees'], function(){
            Route::resource('/','EmployeeController');
        });

        //todo contracts
        Route::group(['prefix'=>'contracts'], function(){
            Route::resource('/','ContractController');
        });

        //todo services
        Route::group(['prefix'=>'services'], function(){
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
        //todo
    });
});
