<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractPlanutilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_planutils', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');  //套餐名
            $table->string('unit');  //套餐单位
            $table->enum('type',['无计划', '有计划'])->default('无计划');   //fixme 这个用来干嘛的忘记了
            $table->enum('type2',['普通','财务','其他'])->default('普通');  //普通为次数套餐, 时间为时间套餐, 便于ServiceController校验
            $table->string('desc', 256)->nullable();  //85字套餐说明
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
        Schema::dropIfExists('contract_planutils');
    }
}
