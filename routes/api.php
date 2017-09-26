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

    //todo Users
    Route::resource('/users', 'UserController');

    //todo company
    Route::group(['prefix'=>'company'], function (){
        Route::get('/page/{page}/{pageSize}','CompanyController@page');
    });
    Route::get('/company/emps/{id}', 'CompanyController@showEmps');
    Route::get('/company/contracts/{id}', 'CompanyController@showContracts');
    Route::get('/company/services/{id}', 'CompanyController@showServices');
    Route::get('/company/channels/{id}', 'CompanyController@showChannels');
        //fixme 分页


    Route::resource('/company','CompanyController');

    //todo emp
    Route::resource('/employee','EmployeeController');


    //todo contracts
    Route::resource('/contracts','ContractController');


    //todo services
    Route::resource('/services','ServiceController');


    //todo channels
    Route::resource('/channels','ChannelController');



    Route::group(['middleware'=>['jwt.auth', 'jwt.refresh']], function (){

    });
});
