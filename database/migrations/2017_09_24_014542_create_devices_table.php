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
            $table->string('ip')->nullable();
            $table->string('sn')->nullable();
            $table->integer('profession_id');
            $table->enum('status', ['停用','重要','一般','自用', '损坏', '专项处理'])->default('一般');
            $table->string('aerial', 30);  //天线 可能是1米, 0.9米, 碳纤维等 10个汉字以内
            $table->string('pa', 30);  //10个汉字以内
            $table->string('lnb',30);  //lnb型号, 10个汉字以内
            $table->timestamp('built_at');
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
