<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_member', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('u_id')->comment('成员ID')->index();
            $table->unsignedInteger('g_id')->comment('组ID')->index();
            $table->unsignedTinyInteger('role')->default(0)->comment('成员角色 0-成员 1-管理员 2-组长');
            $table->unsignedTinyInteger('status')->default(0)->comment('0-正常 1-已申请 2-已拒绝 3：已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `group_member` comment '组成员表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_member');
    }
}
