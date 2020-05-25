<?php

return [
    "app_id"=>  env('WX_APP_ID', ''),
    "app_secret"=>env('WX_APP_SECRET', ''),
    "login_url"=>env('LOGINURL', ''),
    "token_salt" => env('TOKEN_SALT','dlasjdilasiohfoahgsdhiofjaoijdaoi'),
    "token_expire_in" => env('TOKEN_EXPIRE_IN', 7200),
    //Model
    "User"=>"\App\User",
];