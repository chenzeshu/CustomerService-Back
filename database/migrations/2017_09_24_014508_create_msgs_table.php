<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMsgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('msgs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cause_user_id')->nullable();  //触发者-管理员
            $table->integer('cause_emp_id')->nullable();    //触发者-员工/客户联系人
            $table->integer('user_id')->nullable(); //接受者-管理员
            $table->integer('emp_id')->nullable();  //接受者-员工/客户联系人
            $table->string('sms_type',50)->nullable();
            $table->string('email_type',50)->nullable();
            $table->string('service_id',50)->nullable();
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
        Schema::dropIfExists('msgs');
    }
}
