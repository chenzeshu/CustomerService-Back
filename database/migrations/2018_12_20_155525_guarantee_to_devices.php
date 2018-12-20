<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GuaranteeToDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->unsignedInteger('guarantee')->default(3)->comment('质保期：年');
            $table->timestamp('checked_at')->nullable()->comment('最后一次检查时间，第一次时与built_at一致');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
