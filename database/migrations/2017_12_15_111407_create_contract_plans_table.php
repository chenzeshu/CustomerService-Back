<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contract_id');
            $table->integer('plan_id');  //对应planUtils表
            $table->float('total', 8, 2); //总数, 为了兼容金额, 改为float, 8位, 其中2位小数
            $table->float('use', 8, 2)->nullable(); //实际使用次数
            $table->string('desc', 300)->nullable();  //描述性文字, 100字以内
            $table->string('remark', 450)->nullable(); //备注, 150字以内
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
        Schema::dropIfExists('contract_plans');
    }
}
