<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractcPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractc_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contractc_id');
            $table->integer('plan_id');  //信道套餐id
            $table->integer('total');    //套餐购买总时间,  单位为 15分钟
            $table->integer('use')->default(0);   //已用时间, 单位为 15分钟
            $table->string('alias', 33)->default("无名氏套餐");
            $table->string('remark', 150)->nullable();  //默认最多 50个汉字
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
        Schema::dropIfExists('contractc_plans');
    }
}
