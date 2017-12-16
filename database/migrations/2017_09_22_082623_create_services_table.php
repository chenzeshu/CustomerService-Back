<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contract_id');
            $table->string('service_id');
            $table->enum('status', ['待审核', '拒绝', '待派单', '已派单', '申请完成', '已完成', '申述中'])->default('待审核');
//            $table->integer('status_flag')->nullable();  //响应状态 0未处理 1已处理 2超时  todo 直接放前端判断
            $table->timestamp('time3')->nullable();  //响应时间, 记录出现"待审核", "已派单"的时间
            //员工拥有访问申请完成的api的权限, 访问此权限时, 会向管理员发送三位一体通知  所以还要封装一下邮件的类
            $table->integer('source')->nullable();
            $table->integer('type')->nullable();
            $table->string('refer_man')->nullable(); //fixme 提交人
            $table->string('man',50)->nullable();
            $table->string('customer',50);
            $table->enum('charge_if',['收费', '不收费'])->default('不收费');
            $table->enum('charge_flag',['到款', '未到款'])->default('未到款');
            $table->decimal('charge',10,2)->nullable();
            $table->decimal('time4',10,2)->nullable(); //到款时间
            $table->timestamp('time1')->nullable();
            $table->timestamp('time2')->nullable();
            $table->tinyInteger('day_sum')->nullable();  //占用工时
            $table->string('desc1',600)->nullable(); //问题描述  200字以内, 目前不希望放图url
            $table->string('desc2',600)->nullable(); //处理描述 200字以内
            $table->string('remark',600)->nullable(); //备注  200字以内
            $table->string('document',600)->nullable();    //大约最多能放12个文件吧
            $table->string('allege', 600)->nullable(); //申述内容    200字以内
            $table->integer('visit')->nullable(); //外键---访问表
            $table->timestamps();
            //索引
            $table->index('contract_id');
            $table->index('document');
            $table->index('visit');
            $table->index('refer_man');
            $table->index('man');
            $table->index('customer');
//            $table->index('source'); 不做, 因为关联表太小了
//            $table->index('type');    不做, 因为关联表太小
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');

    }
}
