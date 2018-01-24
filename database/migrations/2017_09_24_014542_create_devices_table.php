<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //本表较为严格, 不许为空
        Schema::create('devices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->enum('type',['ad','非ad'])->default('ad');  //ad/非ad
            $table->integer('id5'); //传站类型的id
            $table->string('device_id', 80);  //设备型号, 20个汉字以内
            $table->string('ip');
            $table->timestamp('built_at');  //设备安装时间
            $table->enum('status', ['停用','重要','一般','自用', '损坏', '专项处理'])->default('一般');
            //以下是可以为空的
            $table->string('sn')->nullable();
            $table->integer('profession_id')->nullable();
            $table->string('aerial', 30)->nullable();  //天线 可能是1米, 0.9米, 碳纤维等 10个汉字以内
            $table->string('pa', 30)->nullable();  //10个汉字以内
            $table->string('lnb',30)->nullable();  //lnb型号, 10个汉字以内
            $table->string('remark', 300)->nullable(); //100个汉字以内
            $table->timestamps();

            $table->index('company_id');
//            $table->index('profession_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
}
