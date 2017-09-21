<?php

return [
    "app_id"=>  env('APPID', ''),
    "app_secret"=>env('APPSECRET', ''),
    "login_url"=>env('LOGINURL', ''),
    "token_salt" => env('TOKEN_SALT','dlasjdilasiohfoahgsdhiofjaoijdaoi'),
    "token_expire_in" => env('TOKEN_EXPIRE_IN', 7200),
    //Model
    "User"=>"\App\User",
];