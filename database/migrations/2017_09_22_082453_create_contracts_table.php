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
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('contract_id');
            $table->string('type1');
            $table->enum('type2', ['销售', '客服', '临时'])->default('销售');
            $table->string('PM',50);
            $table->string('TM',50);
            $table->timestamp('time1');
            $table->timestamp('time2')->nullable();
            $table->timestamp('time3')->nullable();
            $table->decimal('money', 10, 2)->nullable();
            $table->text('desc')->nullable();
            $table->text('document')->nullable();
            $table->string('coor')->nullable();
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
        Schema::dropIfExists('contracts');
    }
}
