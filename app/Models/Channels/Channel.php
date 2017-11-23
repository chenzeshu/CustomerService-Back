<?php

namespace App\Models\Channels;

use App\Models\Contractc;
use App\Models\Employee;
use App\Models\Utils\Plan;
use App\Models\Utils\Service_source;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $guarded = [

    ];

    public function channel_applys()
    {
        return $this->hasMany(Channel_apply::class);
    }

    public function contractc()
    {
        return $this->belongsTo(Contractc::class);
    }

    //人可以查到名下的所有信道服务单
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function source()
    {
        return $this->hasOne(Service_source::class, 'id', 'source');
    }

    /**
     * 信道服务单通过apply远层关联套餐
     */
    public function plans()
    {
        return $this->hasManyThrough(
            Plan::class,
            Channel_apply::class,
            'channel_id',
            'id',
            'id',
            'id1');
    }

    /**
     * 通过apply远层通信卫星
     */
    public function tongxin()
    {
        return $this->hasManyThrough(
            Channel_info3::class,
            Channel_apply::class,
            'channel_id',
            'id',
            'id',
            'id2');
    }

    /**
     * 通过apply远层极化
     */
    public function jihua()
    {
        return $this->hasManyThrough(
            Channel_info5::class,
            Channel_apply::class,
            'channel_id',
            'id',
            'id',
            'id3');
    }

    /**
     * 通过apply层频率
     */
    public function pinlv()
    {
        return $this->hasManyThrough(
            Channel_info4::class,
            Channel_apply::class,
            'channel_id',
            'id',
            'id',
            'id4');
    }
}
