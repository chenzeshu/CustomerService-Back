<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckPhonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_phones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id');
            $table->string('code', 6);   //手机为6验证码
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
        Schema::dropIfExists('check_phones');
    }
}
