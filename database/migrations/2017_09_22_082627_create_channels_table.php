<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contractc_id');  //合同编号
            $table->string('channel_id');  //服务单编号
            $table->integer('employee_id');
            $table->enum('status',['待审核', '运营调配', '已完成', '拒绝'])->default('待审核');
            $table->enum('type',['内部用星','外部用星'])->default('外部用星');
            $table->tinyInteger('source')->nullable();  //外键, 来源表的id
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
        Schema::dropIfExists('channels');
    }
}
