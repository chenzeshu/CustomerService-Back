<?php

use Illuminate\Database\Seeder;

class userSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       \Illuminate\Support\Facades\DB::table('users')->insert([
            [
                'name' => '陈泽书',
                'email'=>'1193297950@qq.com',
                'password' => bcrypt('666666'),
                'phone'=>'18502557106',
                'status'=>'online'
            ],
            [
                'name' => '孙雷',
                'email'=>'sun@qq.com',
                'password' => bcrypt('666666'),
                'phone'=>'15951667068',
                'status'=>'online'
            ],
            [
                'name' => '宋小冬',
                'email'=>'song@qq.com',
                'password' => bcrypt('666666'),
                'phone'=>'13611502169',
                'status'=>'online'
            ]
        ]);
    }
}
