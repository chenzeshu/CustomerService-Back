<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChannelsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //todo 以下发生在companySeed中
        //1. 为客户建立信道合同, 每个客户2个
        //dont do 为客户的合同建立信道服务单   | 任何信道服务单都需要人为的申请, 所以没必要一开始就上数据
        //2. 为客户的合同选择套餐, 每个客户2个

        //todo 找出所有信道合同, 为每个信道合同创建2个套餐
            //factory->make()出就是return的数组
        \App\Models\Contract_c::all()->each(function ($contract){
            $contract->channel_plans()->save(factory(\App\Models\Channels\Channel_plan::class)->make());
            $contract->channel_plans()->save(factory(\App\Models\Channels\Channel_plan::class)->make());
        });

        //todo 为客户的每个信道套餐mock出至少2条数据
        \App\Models\Channels\Channel_plan::all()->each(function ($plan){
           $plan->channel_details()->save(factory(\App\Models\Channels\Channel_detail::class)->make());
        });
    }
}
