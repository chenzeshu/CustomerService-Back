<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelRealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_reals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('channel_apply_id');
            $table->integer('checker_id')->nullable();   //本次信道使用的确认/负责人的id!  若为空, 下次对应的单位无法再申请信道
            $table->integer('id1'); //用户套餐表的id 必填
            $table->integer('id2');
            $table->integer('id3');
            $table->integer('id4'); //带宽
            $table->timestamp('t1')->nullable();  //开始时间 必填
            $table->timestamp('t2')->nullable();  //结束时间 必填
            $table->string('remark', 450)->nullable(); //150个汉字以内
            $table->timestamps();

            $table->index('id1');  //子级索引
            $table->index('id2');  //子级索引
            $table->index('id3');  //子级索引
            $table->index('id4');  //子级索引
            $table->index('channel_apply_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channel_reals');
    }
}
