<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/7
 * Time: 9:12
 */

namespace App\Observers;


use App\Id_record;
use App\Models\Channels\Channel;
use Illuminate\Support\Facades\Log;

class ChannelObserver
{
    /**
     * 监听创建/更新
     */
    public function saved()
    {
        Log::info('channelComing');
        Channel::forget_cache();
    }

    public function created()
    {
        Id_record::find(5)->increment('record');
    }

    /**
     * 监听删除
     */
    public function deleted()
    {
        Channel::forget_cache();
    }
}