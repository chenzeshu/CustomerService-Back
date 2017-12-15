<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_id');
            $table->enum('result_deal', ['待解决','已解决','未解决'])->default('待解决');
            $table->integer('result_rating')->default(4);
            $table->integer('result_visit')->default(4);
            $table->timestamp('time')->nullable();  //回访时间, 若设置已解决, 但未回访, 本时间为空
            $table->string('visitor',50)->nullable();  //fixme 所以这个visitor 不是让管理员选的, 而是默认填写管理员的id
            $table->string('remark', 200)->nullable();  //200子以内
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
        Schema::dropIfExists('visits');
    }
}
