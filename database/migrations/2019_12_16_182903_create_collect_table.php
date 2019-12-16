<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collect', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('u_id')->comment('收藏者ID')->index();
            $table->unsignedInteger('t_id')->comment('主题id');
            $table->unsignedTinyInteger('status')->default(0)->comment('0：正常 1：已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `collect` comment '收藏表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collect');
    }
}
