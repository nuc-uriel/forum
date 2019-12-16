<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('u_id')->comment('举报者ID');
            $table->unsignedInteger('target_id')->comment('目标id');
            $table->unsignedTinyInteger('type')->default(0)->comment('目标类型：0：用户 1：小组 2：讨论 3：评论');
            $table->string('content', 2048)->default('')->comment('内容');
            $table->unsignedTinyInteger('status')->default(0)->comment('0：正常 1：已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `report` comment '举报表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report');
    }
}
