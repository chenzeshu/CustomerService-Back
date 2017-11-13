<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contractc_id');
            $table->integer('plan');  //系统信道套餐表的id
            $table->integer('full_time');  //本套餐总购买时间  单位:分钟
            $table->enum('flag', ['用完', '正常'])->default('正常');
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
        Schema::dropIfExists('channel_plans');
    }
}
