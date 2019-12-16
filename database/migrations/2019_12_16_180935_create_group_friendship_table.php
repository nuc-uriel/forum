<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupFriendshipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_friendship', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('go_id')->comment('主人组ID')->index();
            $table->unsignedInteger('gf_id')->comment('朋友组ID')->index();
            $table->unsignedTinyInteger('status')->default(0)->comment('0：正常，1：已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `group_friendship` comment '友情组表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_friendship');
    }
}
