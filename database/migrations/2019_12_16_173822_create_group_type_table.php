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
            $table->unsignedTinyInteger('status')->default(0)->comment('0：使用中，1：已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `group_type` comment '组类别表'");
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
