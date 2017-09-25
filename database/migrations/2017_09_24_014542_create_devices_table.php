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
            $table->integer('type');  //ad/非ad
            $table->integer('id5'); //传站类型的id
            $table->string('device_id');
            $table->string('ip')->nullable();
            $table->string('s/n')->nullable();
            $table->integer('profession_id');
            $table->enum('status', ['停用','重要','一般','自用', '损坏', '专项处理'])->default('一般');
            $table->string('aerial');
            $table->string('pa');
            $table->string('lnb');  //lnb型号
            $table->timestamp('built_at');
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
        Schema::dropIfExists('devices');
    }
}
