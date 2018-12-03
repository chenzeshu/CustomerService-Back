<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * fixme ...
         * 一个缺点：没有中间表，没有同步，因此假设服务单被删除时，问题的同步很麻烦（回溯不到服务单）
         * ---》因为时间太紧了。最好的办法就是下一期仔细考虑各种数据同步问题和try catch。
         */

        /**
         * 问题主表
         */
        Schema::create('problems', function (Blueprint $table) {
            $table->increments('problem_id');
//            $table->unsignedInteger('device_id')->nullable()->comment('设备id，但问题不一定关联设备，只需要选到故障大类即可');
            $table->unsignedInteger('service_id')->nullable()->comment('服务单id外键');
            $table->unsignedInteger('problem_type')->default(1)->comment('问题类型的外键，必须选择一个种类');
            $table->enum('problem_step', ['未解决', '运维解决中', '技术或厂商解决中', '专家解决中', '已解决'])->default('未解决')->comment('问题解决步骤');
            $table->text('problem_desc')->comment('问题的描述');
            $table->text('problem_solution')->nullable()->comment('问题的解决方法');
            $table->enum('problem_importance', ['一般', '重要', '非常重要'])->nullable('一般')->comment('问题的重要程度');
            $table->enum('problem_urgency', ['一般', '紧急', '非常紧急'])->nullable('一般')->comment('问题的紧急程度');
            $table->text('problem_remark')->nullable()->comment('备注');
            $table->timestamps();

            $table->index('service_id');
        });

        /**
         * 问题类型表
         */
        Schema::create('problem_types', function (Blueprint $table) {
            $table->increments('ptype_id');
            $table->string('ptype_name', 32)->comment('问题类型名称');
            $table->string('ptype_remark', 128)->comment('关于此名称的备注');
            $table->timestamps();
        });

        /**
         * 报警记录表
         */
        Schema::create('problem_records', function (Blueprint $table) {
            $table->increments('precord_id');
            $table->timestamps();
        });

        /**
         * 故障表与设备表的中间表
         */
        Schema::create('problem_devices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('problem_id');
            $table->unsignedInteger('device_id');
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
        Schema::dropIfExists('problems');
        Schema::dropIfExists('problem_types');
        Schema::dropIfExists('problem_records');
        Schema::dropIfExists('problem_devices');
    }
}
