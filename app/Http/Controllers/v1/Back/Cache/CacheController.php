<?php

namespace App\Http\Controllers\v1\Back\Cache;

use App\Models\Channels\Channel_info3;
use App\Models\Channels\Channel_info4;
use App\Models\Channels\Channel_info5;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class CacheController extends Controller
{
    //用于制作各种内部用缓存接口, 不放在jwt里面,  所以api要专门加密或变动
    public function weeklyStore()
    {
        $this->tongxin();
        $this->jihua();
        $this->pinlv();
    }


    private function tongxin()
    {
        $tongxin = Channel_info3::all()->toArray();
        Cache::put('utils_tongxin', $tongxin, 604800);
    }

    private function jihua()
    {
        $jihua = Channel_info5::all()->toArray();
        Cache::put('utils_tongxin', $jihua, 604800);
    }

    private function pinlv()
    {
        $pinlv = Channel_info4::all()->toArray();
        Cache::put('utils_tongxin', $pinlv, 604800);
    }
}
