<?php

namespace App\Models;

use App\Models\Channels\Channel;
use App\Models\Channels\Channel_duty;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class Employee extends Model
{
    protected $guarded = [

    ];

    protected $hidden = [
//        'openid'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    //查人物下的信道服务单, 方便调取服务单状态
    public function channels()
    {
        return $this->hasMany(Channel::class);
    }

    public static function authenticate($token)
    {
        $part = explode(".", $token);
        $payload = $part[1];
        $_payload = base64_decode($payload);
        $_payload = json_decode($_payload, true); //返回数组
        if(time() > $_payload['exp']){
            throw new TokenExpiredException();
        }
        Employee::findOrFail($_payload['sub']);
    }
}
