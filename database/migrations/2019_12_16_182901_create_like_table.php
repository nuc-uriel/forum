<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLikeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('like', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('u_id')->comment('点赞者ID')->index();
            $table->unsignedInteger('t_id')->comment('主题id');
            $table->unsignedInteger('target_id')->comment('目标ID，评论则为主题id，回复则为评论id')->index();
            $table->unsignedTinyInteger('type')->default(0)->comment('1：评论 2：回复');
            $table->unsignedTinyInteger('status')->default(0)->comment('0：正常 1：已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `like` comment '点赞表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('like');
    }
}
