<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //回访表
        Schema::create('employee_visits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->nullable(); //如果是员工回访
            $table->integer('user_id')->nullable();  //如果是管理员回访
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
        Schema::dropIfExists('employee_visits');
    }
}
