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
        //服务合同总表
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('name'); //合同名称
            $table->decimal('money', 10, 2)->nullable();
            $table->string('contract_id');
            $table->string('type1');        //1-3分别是 集成、服务、综合
            $table->enum('type2', ['销售', '客服'])->default('销售');
            $table->string('PM',50);
            $table->string('TM',50)->nullable();
            $table->timestamp('time1')->nullable();
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

        //合同类型
        Schema::create('contract_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30);  //最多10个汉字
            $table->timestamps();
        });

        //服务合同套餐定义表
        Schema::create('contract_planutils', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');  //套餐名
            $table->string('unit');  //套餐单位
            $table->enum('type',['无计划', '有计划'])->default('无计划');   //fixme 这个用来干嘛的忘记了
            $table->enum('type2',['普通','财务','其他'])->default('普通');  //普通为次数套餐, 时间为时间套餐, 便于ServiceController校验
            $table->string('desc', 256)->nullable();  //85字套餐说明
            $table->timestamps();
        });

        //服务合同套餐用量表
        Schema::create('contract_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contract_id');
            $table->integer('plan_id');  //对应planUtils表
            $table->float('total', 8, 2); //总数, 为了兼容金额, 改为float, 8位, 其中2位小数
            $table->float('use', 8, 2)->default(0); //实际使用次数
            $table->string('desc', 300)->nullable();  //描述性文字, 100字以内
            $table->string('remark', 450)->nullable(); //备注, 150字以内
            $table->timestamps();
            //索引
            $table->index('contract_id');
            $table->index('plan_id');
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
        Schema::dropIfExists('contract_types');
        Schema::dropIfExists('contract_planutils');
        Schema::dropIfExists('contract_plans');
    }
}
