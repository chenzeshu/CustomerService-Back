<?php

use Illuminate\Http\Request;

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

Route::group(['prefix'=>'v1'], function (){
    Route::get('test', function (){
        return bcrypt('666666');
    });
    Route::post('/login', 'v1\Back\LoginController@login');
    //todo 开发完后将需要jwt验证的模块放入下方;
    

    Route::group(['middleware'=>['jwt.auth', 'jwt.refresh']], function (){

    });
});
