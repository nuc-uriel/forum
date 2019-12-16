<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_type', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 32)->default('')->comment('组类别名称')->index();
            $table->string('introduce', 128)->default('')->comment('类型介绍');
            $table->unsignedTinyInteger('status')->default(0)->comment('0：使用中，1：已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `group_type` comment '组类别表'");
        $initData = array(
            array(
                'name'=>'文化',
                'introduce'=>'文学、语言、人文、建筑、哲学、宗教、展览',
                'status'=>0
            ),array(
                'name'=>'行摄',
                'introduce'=>'旅行、摄影',
                'status'=>0
            ),array(
                'name'=>'娱乐',
                'introduce'=>'影视、音乐、动漫、游戏、桌游',
                'status'=>0
            ),array(
                'name'=>'时尚',
                'introduce'=>'美容、护肤、化妆、育儿',
                'status'=>0
            ),array(
                'name'=>'生活',
                'introduce'=>'运动、手工、宠物、美食',
                'status'=>0
            ),array(
                'name'=>'科技',
                'introduce'=>'数码、互联网、软件、硬件',
                'status'=>0
            )
        );
        foreach ($initData as $gt){
            \App\GroupType::create($gt);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_type');
    }
}
