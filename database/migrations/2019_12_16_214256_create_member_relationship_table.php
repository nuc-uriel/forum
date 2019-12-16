<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberRelationshipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_relationship', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('myself_id')->comment('主人ID');
            $table->unsignedInteger('other_id')->comment('关系人ID');
            $table->unsignedTinyInteger('type')->default(0)->comment('关系类型 0-关注 1-黑名单');
            $table->unsignedTinyInteger('status')->default(0)->comment('0-正常 1-已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `member_relationship` comment '用户关系表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_relationship');
    }
}
