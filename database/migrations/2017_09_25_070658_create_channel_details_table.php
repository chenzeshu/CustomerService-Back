<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('channel_plan_id');
            $table->integer('time')->default(15);  //使用了几分钟, 不补足15分钟按15分钟计 1=>15, 16=>30, 31=>45
            $table->text('desc')->nullable();
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
        Schema::dropIfExists('channel_details');
    }
}
