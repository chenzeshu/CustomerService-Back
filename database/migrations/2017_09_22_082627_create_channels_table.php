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
            $table->enum('status', config('app.channel.status'))->default('待审核');
            $table->enum('type',config('app.channel.stars'))->default('外部用星');
            $table->tinyInteger('source')->nullable();  //外键, 来源表的id
            $table->index('contractc_id'); //合同编号作为索引之一
            $table->index('employee_id'); //申请人索引
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
