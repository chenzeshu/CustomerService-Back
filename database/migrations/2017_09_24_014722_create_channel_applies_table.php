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
            $table->integer('employee_id');
            $table->integer('id1'); //用户套餐表的id 必填
            $table->integer('id2')->nullable();
            $table->integer('id3')->nullable();
            $table->integer('id4')->nullable();
            $table->timestamp('t1');  //申请时间 必填
            $table->timestamp('t2');    //结束时间 必填
            $table->text('remark')->nullable();
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
