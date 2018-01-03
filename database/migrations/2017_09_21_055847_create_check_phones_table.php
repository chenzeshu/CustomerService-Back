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
            $table->string('phone', 20);  //防止以后改验证, +86等出现
            $table->char('code', 6);   //手机为6验证码
            $table->tinyInteger('status')->default(0); //0:有效, 1:失效
            $table->timestamp('expire_at');  //5分钟过期时间, 未过期的都可以使用//之所以短是因为避免碰撞
            //并可以统计一段时间内的注册量来决定使用几位验证码
            $table->timestamps();
            $table->index(['phone', 'status']);  //验证时, 通过索引组合检索出来N个有效code, 并匹配
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
