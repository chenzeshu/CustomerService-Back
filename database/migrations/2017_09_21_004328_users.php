<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Users extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table){
            $table->increments('id');
            $table->string('name');        //必须填写
            $table->string('password');    //必须填写
            $table->string('email',64)->unique();       //必须填写
            $table->string('phone',16)->unique();       //必须填写
            $table->string('remember_token')->nullable();   //以备auth模块
            $table->integer('scope')->default(32);  //权限等级
            $table->enum('status',['offline', 'online'])->default('online');  //根据员工离职情况 进行账号状态选择(而非删除账号)
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
        Schema::dropIfExists('users');
    }
}
