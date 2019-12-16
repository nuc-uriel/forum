<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('u_id')->comment('创建者ID');
            $table->unsignedInteger('gt_id')->comment('组类别ID');
            $table->string('name', 32)->default('')->comment('组名称')->index();
            $table->string('avatar', 125)->default('')->comment('组头像');
            $table->string('introduce', 5000)->default('')->comment('组介绍');
            $table->unsignedTinyInteger('status')->default(0)->comment('0：正常，1：待审核，2：审核未通过，3：被封禁，:4：已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `group` comment '组类别表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group');
    }
}
