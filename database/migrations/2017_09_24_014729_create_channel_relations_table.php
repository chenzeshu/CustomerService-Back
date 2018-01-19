<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_relations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('channel_apply_id');
            $table->integer('company_id');
            $table->integer('device_id');       //关联设备表的设备id
            $table->integer('id5');//占类型
            $table->index('channel_apply_id');  //父级索引
            $table->index('device_id');  //子级索引
            $table->index('id5');  //子级索引
            $table->index('company_id');  //子级索引
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
        Schema::dropIfExists('channel_relations');
    }
}
