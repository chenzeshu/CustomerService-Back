<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractCsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractcs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('contract_id');  //信道合同编号
            $table->string('name');  //合同名称
            $table->string('PM',50);
            $table->timestamp('time');
            $table->decimal('money', 10,2); //合同金额
            $table->timestamp('beginline')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->string('desc',450)->nullable(); //150个汉字以内
            $table->text('document' ,600)->nullable();  //大约存6个文件
            $table->timestamps();
        });

        //信道合同历次到款详情
        Schema::create('channel_moneys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contractc_id');  //orm
            $table->enum('type', ['分次付款', '不分次'])->default('不分次');
            $table->enum('finish', ['未结清', '结清'])->default('未结清');
            $table->timestamp('t1')->nullable();  //约定总收款时间
            $table->timestamp('t2')->nullable();  //实际总到款时间
            $table->integer('num')->default(1);  //分次次数, 默认一次付清
            $table->integer('checker_id')->nullable();   //必须要有一个负责人
            $table->timestamps();
        });

        //信道合同到款情况表
        Schema::create('channel_money_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('channel_money_id');
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
        Schema::dropIfExists('contractcs');
        Schema::dropIfExists('channel_moneys');
        Schema::dropIfExists('channel_money_details');
    }
}
