<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_mails', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id');
            $table->string('code', 32);   //邮件为32位md5验证码, 作为get参数附在url后
            $table->integer('status')->default(0); //0:未验证, 1:已验证
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
        Schema::dropIfExists('check_mails');
    }
}
