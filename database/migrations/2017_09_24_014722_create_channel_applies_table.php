<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelAppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_applies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('channel_id');
            $table->integer('id1'); //用户套餐表的id 必填
            $table->integer('id2')->nullable();
            $table->integer('id3')->nullable();
            $table->integer('id4')->nullable();  //带宽
            $table->timestamp('t1')->nullable();  //开始时间 必填
            $table->timestamp('t2')->nullable();  //结束时间 必填
            $table->string('remark', 450)->nullable();  //150个汉字以内
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
        Schema::dropIfExists('channel_applies');
    }
}
