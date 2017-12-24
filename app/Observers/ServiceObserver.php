<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/24
 * Time: 15:46
 */

namespace App\Observers;


use App\Id_record;

class ServiceObserver
{
    public function created()
    {
        Id_record::find(4)->increment('record');
    }
}