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
            $table->timestamp('beginline')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->decimal('money', 10, 2)->nullable();
            $table->text('desc')->nullable();
            $table->text('document')->nullable();
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
    }
}
