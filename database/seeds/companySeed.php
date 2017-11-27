<?php

use Illuminate\Database\Seeder;

class companySeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Company::class, config('app.seeding.company'))->create()->each(function ($company){
            //todo 每个单位的人员
            factory(App\Models\Employee::class, 2)->make()->each(function ($employee) use ($company){
                $company->employees()->save($employee);
            });
            //todo 每个单位的设备
            factory(\App\Models\Utils\Device::class, config('app.seeding.device'))->make()->each(function ($device) use ($company){
                $company->employees()->save($device);
            });
            //todo 制造2个普通合同 + 2个服务单/合同
            factory(\App\Models\Contract::class, config('app.seeding.contract'))->make()->each(function ($contract) use ($company){
                $company->contracts()->save($contract);
                factory(\App\Models\Services\Service::class, config('app.seeding.service'))->make()->each(function ($service) use ($contract){
                   $contract->services()->save($service);
                   //todo 回访
                   factory(\App\Models\Services\Visit::class, 1)->make()->each(function ($visit) use ($service){
                      $service->visits()->save($visit);
                   });
                });
            });

            //todo 制造2个信道合同 + 2个套餐/合同
            factory(\App\Models\Contractc::class, config('app.seeding.contract_c'))->make()->each(function ($contract_c) use ($company){
                $company->contract_cs()->save($contract_c);
                factory(App\Models\Channels\Channel::class, config('app.seeding.channel'))->make()->each(function ($channel) use ($contract_c){
                    //信道服务单
                    $contract_c->channels()->save($channel);
                    //信道申请单
                    factory(\App\Models\Channels\Channel_apply::class, 1)->make()->each(function ($apply) use ($channel){
                       $channel->channel_applys()->save($apply);

                        factory(\App\Models\Channels\Channel_operative::class, 1)->make()->each(function ($o) use ($apply){
                            $apply->channel_operative()->save($o);
                        });
                        factory(\App\Models\Channels\Channel_real::class, 1)->make()->each(function ($real) use ($apply){
                            $apply->channel_real()->save($real);
                        });
                        factory(\App\Models\Channels\Channel_relation::class, rand(1,4))->make()->each(function ($relation) use ($apply){  //1-4个节点
                            $apply->channel_relations()->save($relation);
                        });
                    });
                });

                factory(\App\Models\Channels\Channel_plan::class, config('app.seeding.plan'))->make()->each(function ($plan) use ($contract_c){
                    //套餐/合同
                    $contract_c->channel_plans()->save($plan);
                });
            });

        });
    }
}
