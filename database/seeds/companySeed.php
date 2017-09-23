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
        factory(App\Models\Company::class, 50)->create()->each(function ($company){
            //每个单位做2个人
            $company->employees()->save(factory(App\Models\Employee::class)->make());
            $company->employees()->save(factory(App\Models\Employee::class)->make());
            //合同
            $company->contracts()->save(factory(\App\Models\Contract::class)->make());
            $company->contracts()->save(factory(\App\Models\Contract::class)->make());

            $company->contracts()->get()->each(function ($contract){
                //普通服务单
                $contract->services()->save(factory(\App\Models\Services\Service::class)->make());
                //信道服务单
                $contract->channels()->save(factory(\App\Models\Channels\Channel::class)->make());
            });
            //回访表

        });
    }
}
