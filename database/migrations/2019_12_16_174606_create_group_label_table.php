<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupLabelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_label', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('g_id')->default(0)->comment('组ID');
            $table->string('name', 32)->default('')->comment('组标签名称')->index();
            $table->unsignedTinyInteger('status')->default(0)->comment('0：正常，1：已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `group_label` comment '组标签表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_label');
    }
}
