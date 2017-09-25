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

            //制造2个普通合同
            $company->contracts()->save(factory(\App\Models\Contract::class)->make());

            //制造2个信道合同及对应的套餐
            $company->contract_cs()->save(factory(\App\Models\Contract_C::class)->make());
            $company->contract_cs()->save(factory(\App\Models\Contract_C::class)->make());

            //每个单位3个设备
            $company->devices()->save(factory(\App\Models\Utils\Device::class)->make());
            $company->devices()->save(factory(\App\Models\Utils\Device::class)->make());
            $company->devices()->save(factory(\App\Models\Utils\Device::class)->make());
            //回访表

        });
    }
}
