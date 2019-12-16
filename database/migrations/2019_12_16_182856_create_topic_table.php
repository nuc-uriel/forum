<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('u_id')->comment('作者ID')->index();
            $table->unsignedInteger('g_id')->comment('小组ID')->index();
            $table->string('title', 128)->default('')->comment('标题');
            $table->text('content')->default('')->comment('内容');
            $table->unsignedTinyInteger('is_top')->default(0)->comment('0：正常 1：置顶');
            $table->unsignedTinyInteger('can_comment')->default(0)->comment('0：正常 1：禁止回复');
            $table->unsignedTinyInteger('status')->default(0)->comment('0：正常 1：被封禁 2：已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `topic` comment '主题表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('topic');
    }
}
