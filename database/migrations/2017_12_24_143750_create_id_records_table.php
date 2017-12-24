<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //本表每年1月1日0:00 refresh
        Schema::create('id_records', function (Blueprint $table) {
            $table->increments('id');  //[1=>销字合同计数, 2=>客字合同计数, 3=>信合计数, 4=>客服, 5=>信服]
            $table->integer('record')->default(1);  //[001->999]
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
        Schema::dropIfExists('id_records');
    }
}
