<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('u_id')->comment('评论者ID')->index();
            $table->unsignedInteger('t_id')->comment('主题id');
            $table->unsignedInteger('parent_id')->comment('父级ID，评论则为主题id，回复则为评论id')->index();
            $table->string('content', 5000)->default('')->comment('内容');
            $table->string('image', 128)->default('')->comment('图片路径');
            $table->unsignedTinyInteger('type')->default(0)->comment('1：评论 2：回复');
            $table->unsignedTinyInteger('status')->default(0)->comment('0：正常 1：已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `comment` comment '评论表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comment');
    }
}
