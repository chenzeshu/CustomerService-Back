<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 17:06
 */

namespace App\Observers;


use App\Models\Channels\Channel;

class ApplyObserver
{
    /**
     * 监听创建/更新
     */
    public function saved()
    {
        Channel::forget_cache();
    }

    /**
     * 监听删除
     */
    public function deleted()
    {
        Channel::forget_cache();
    }
}