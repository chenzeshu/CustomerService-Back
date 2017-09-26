<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //无依赖部分
        $this->call(ChannelsUtilsSeed::class);
        $this->call(UtilsSeed::class);
        $this->call(companySeed::class);
        $this->call(userSeed::class);
        //有依赖部分

    }
}
