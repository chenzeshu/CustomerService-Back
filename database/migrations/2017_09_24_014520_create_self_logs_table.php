<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelfLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //本表还没有正式启用, 最后再搞
        //并不是每个动作都要记录
        Schema::create('self_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('contents');
            $table->integer('type'); //日志分类: 服务单类 注册类 权限分配类等
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
        Schema::dropIfExists('self_logs');
    }
}
