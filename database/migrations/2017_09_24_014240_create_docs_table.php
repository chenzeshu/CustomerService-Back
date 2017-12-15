<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docs', function (Blueprint $table) {
            $table->increments('id');  //要不要md5防碰撞?
            $table->string('name', 100);   //假设文件标题为30个汉字, 90字节, 给100字节
            $table->string('path',128);  //基本路径长度约25字节, 假设文件标题为30个汉字, 即总共115字节, 实际给128字节.
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
        Schema::dropIfExists('docs');
    }
}
