<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('username', 32)->default('')->comment('昵称')->index();
            $table->string('avatar', 128)->default('')->comment('用户头像');
            $table->unsignedTinyInteger('sex')->default(0)->comment('性别');
            $table->unsignedTinyInteger('age')->default(0)->comment('年龄');
            $table->string('place', 32)->default('')->comment('居住地');
            $table->char('password', 40)->default('')->comment('用户密码(sha1加密)');
            $table->string('signature', 128)->default('')->comment('签名');
            $table->string('introduce', 5000)->default('')->comment('个人介绍');
            $table->unsignedTinyInteger('status')->default(0)->comment('0：未验证邮箱，1：已验证邮箱，2：被封禁，3：已删除');
            $table->string('confirmation', 32)->default('')->comment('激活码');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `user` comment '用户表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
}
