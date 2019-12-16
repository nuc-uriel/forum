<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('uf_id')->comment('发起人ID')->index();
            $table->unsignedInteger('ut_id')->comment('接收人ID')->index();
            $table->string('content', 2048)->default('')->comment('内容');
            $table->string('group_code', 16)->default('')->comment('分组码(md5(min(uf_id,ut_id), max(uf_id,ut_id)))');
            $table->unsignedTinyInteger('status')->default(0)->comment('0：未读，1：已读，3：已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `message` comment '用户消息表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message');
    }
}
