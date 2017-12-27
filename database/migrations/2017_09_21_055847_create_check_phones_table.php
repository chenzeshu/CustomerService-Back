<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckPhonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_phones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('phone', 20);
            $table->string('code', 6);   //手机为6验证码
            $table->tinyInteger('status')->default(0); //0:有效, 1:失效
            $table->timestamp('expire')->default(300);  //5分钟过期时间, 未过期的都可以使用//之所以短是因为避免碰撞
            //并可以统计一段时间内的注册量来决定使用几位验证码
            $table->timestamps();

            $table->index(['code', 'status']);  //验证时, 查找输入的有效的code, 出来N个数据, 找到最新的一条, 匹配手机号, 正确则将code失效
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_phones');
    }
}
