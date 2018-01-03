<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeWatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //待审核名单
        Schema::create('employee_waitings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30);
            $table->string('company', 120);  //单位名称
            $table->string('openid')->unique();   //必须填写(登陆的时候必然存在)
            $table->string('avatar',256)->nullable();       //头像, 保存url
            $table->string('email',64)->nullable();       //必须填写
            $table->string('phone',30)->unique();       //必须填写
            $table->enum('status', ['通过', '未通过', '拒绝'])->default('未通过');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_waitings');
    }
}
