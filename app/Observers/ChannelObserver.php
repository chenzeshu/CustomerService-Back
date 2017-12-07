<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/7
 * Time: 9:12
 */

namespace App\Observer;


use App\Models\Channels\Channel;

class ChannelObserver
{
    /**
     * 监听创建/更新
     */
    public function saved(Channel $channel)
    {
        Channel::redis_refresh_data();
    }

    /**
     * 监听删除
     */
    public function deleted()
    {
        Channel::redis_refresh_data();
    }
}