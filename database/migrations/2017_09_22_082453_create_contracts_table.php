<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('name'); //合同名称
            $table->decimal('money', 10, 2)->nullable();
            $table->string('contract_id');
            $table->string('type1');
            $table->enum('type2', ['销售', '客服'])->default('销售');
            $table->string('PM',50);
            $table->string('TM',50)->nullable();
            $table->timestamp('time1');
            $table->timestamp('time2')->nullable();
            $table->timestamp('time3')->nullable();
            $table->text('desc')->nullable();
            $table->text('document')->nullable();
            $table->string('coor')->nullable();
            $table->timestamps();
        });

        //服务合同到款表
        Schema::create('service_moneys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contract_id');  //orm
            $table->enum('type', ['分次付款', '不分次'])->default('不分次');
            $table->enum('finish', ['未结清', '结清'])->default('未结清');
            $table->timestamp('t1')->nullable();  //约定总收款时间
            $table->timestamp('t2')->nullable();  //实际总到款时间
            $table->integer('num')->default(1);  //分次次数, 默认一次付清
            $table->integer('checker_id')->nullable();   //必须要有一个负责人
            $table->timestamps();
        });

        //服务合同历次到款详情
        Schema::create('service_money_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_money_id');
            $table->decimal('money', 10, 2)->nullable();
            $table->timestamp('t1')->nullable();  //约定收款时间
            $table->timestamp('t2')->nullable();  //实际到款时间
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
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('service_moneys');
        Schema::dropIfExists('service_money_details');
    }
}
