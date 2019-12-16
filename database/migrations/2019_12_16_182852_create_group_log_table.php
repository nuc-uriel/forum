<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_log', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('g_id')->comment('组ID')->index();
            $table->unsignedInteger('u_id')->comment('操作者ID');
            $table->unsignedTinyInteger('type')->default(0)->comment('日志类型')->index();
            $table->string('content', 2048)->default('')->comment('日志内容');
            $table->unsignedTinyInteger('status')->default(0)->comment('0-正常 1-已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `group_log` comment '组日志表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_log');
    }
}
