<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('company_id');
            $table->string('openid')->unique();   //必须填写(登陆的时候必然存在)
            $table->string('avatar',256)->nullable();       //头像, 保存url
            $table->string('email',64)->unique();       //必须填写
            $table->string('phone',30)->unique();       //必须填写
            $table->enum('status', ['offline', 'online', '离职', '拒绝'])->default('offline');  //不管待审批表里的最终被拒绝还是如何, 都会进入这个表, 成为记录
            $table->timestamp('changed_at')->nullable();  //专用于记录审核通过时间, 区分后期更改邮箱或手机时间, 可为空
            $table->index('company_id');  //父级索引
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
        Schema::dropIfExists('employees');
    }
}
