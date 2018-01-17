<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/13
 * Time: 18:53
 */

namespace App\Dao;


class ChannelDAO
{

    public static function getChannelStats()
    {
        return config('app.channel.status');
    }

    /**
     * 得到内部用星, 外部用星
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function getStars()
    {
        return config('app.channel.starts');
    }
}