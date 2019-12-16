<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupBanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_ban', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('g_id')->comment('组违禁词ID')->index();
            $table->unsignedInteger('u_id')->comment('组ID');
            $table->string('word', 32)->default('')->comment('违禁词');
            $table->unsignedTinyInteger('status')->default(0)->comment('0-正常 1-已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `group_ban` comment '组违禁词表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_ban');
    }
}
