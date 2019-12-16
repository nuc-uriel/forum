<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inform', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('code', 32)->default('')->comment('唯一标识码')->unique();
            $table->unsignedInteger('uf_id')->comment('发起人ID')->index();
            $table->unsignedInteger('ut_id')->comment('接收人ID');
            $table->unsignedInteger('relevance_id')->comment('关联记录ID');
            $table->unsignedTinyInteger('type')->default(0)->comment('通知类型');
            $table->string('content', 2048)->default('')->comment('通知内容');
            $table->unsignedTinyInteger('is_dispose')->default(0)->comment('是否需要处理 0：不需要 1：需要');
            $table->unsignedInteger('disposer_id')->comment('处理者ID');
            $table->unsignedTinyInteger('status')->default(0)->comment('0：未读，1：已读，1：已同意，2：已拒绝，3：已删除');
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE `inform` comment '通知表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inform');
    }
}
