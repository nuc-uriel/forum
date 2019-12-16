<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitAdminMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('admin:install');
        DB::table('admin_menu')->truncate();
        DB::table('admin_menu')->insert([
            [
                "id" => 1,
                "parent_id" => 0,
                "order" => 1,
                "title" => "首页",
                "icon" => "fa-bar-chart",
                "uri" => "/",
            ],
            [
                "id" => 2,
                "parent_id" => 0,
                "order" => 2,
                "title" => "后台管理",
                "icon" => "fa-tasks",
                "uri" => "",
            ],
            [
                "id" => 3,
                "parent_id" => 2,
                "order" => 3,
                "title" => "管理员列表",
                "icon" => "fa-users",
                "uri" => "auth/users",
            ],
            [
                "id" => 4,
                "parent_id" => 2,
                "order" => 4,
                "title" => "角色列表",
                "icon" => "fa-user",
                "uri" => "auth/roles",
            ],
            [
                "id" => 5,
                "parent_id" => 2,
                "order" => 5,
                "title" => "权限列表",
                "icon" => "fa-user",
                "uri" => "auth/permissions",
            ],
            [
                "id" => 6,
                "parent_id" => 2,
                "order" => 6,
                "title" => "菜单设置",
                "icon" => "fa-bars",
                "uri" => "auth/menu",
            ],
            [
                "id" => 7,
                "parent_id" => 2,
                "order" => 7,
                "title" => "操作日志",
                "icon" => "fa-history",
                "uri" => "auth/logs",
            ],
            [
                "id" => 8,
                "parent_id" => 0,
                "order" => 19,
                "title" => "帮助工具",
                "icon" => "fa-gears",
                "uri" => "",
            ],
            [
                "id" => 9,
                "parent_id" => 8,
                "order" => 20,
                "title" => "脚手架工具",
                "icon" => "fa-keyboard-o",
                "uri" => "helpers/scaffold",
            ],
            [
                "id" => 10,
                "parent_id" => 8,
                "order" => 21,
                "title" => "数据库命令行",
                "icon" => "fa-database",
                "uri" => "helpers/terminal/database",
            ],
            [
                "id" => 11,
                "parent_id" => 8,
                "order" => 22,
                "title" => "artisan命令行",
                "icon" => "fa-terminal",
                "uri" => "helpers/terminal/artisan",
            ],
            [
                "id" => 12,
                "parent_id" => 13,
                "order" => 9,
                "title" => "用户列表",
                "icon" => "fa-user",
                "uri" => "/users",
            ],
            [
                "id" => 13,
                "parent_id" => 0,
                "order" => 8,
                "title" => "用户管理",
                "icon" => "fa-users",
                "uri" => "",
            ],
            [
                "id" => 14,
                "parent_id" => 0,
                "order" => 10,
                "title" => "小组管理",
                "icon" => "fa-object-group",
                "uri" => "",
            ],
            [
                "id" => 15,
                "parent_id" => 14,
                "order" => 11,
                "title" => "小组列表",
                "icon" => "fa-object-ungroup",
                "uri" => "groups",
            ],
            [
                "id" => 16,
                "parent_id" => 0,
                "order" => 13,
                "title" => "讨论管理",
                "icon" => "fa-file-powerpoint-o",
                "uri" => "topics",
            ],
            [
                "id" => 17,
                "parent_id" => 16,
                "order" => 14,
                "title" => "讨论列表",
                "icon" => "fa-th-list",
                "uri" => "topics",
            ],
            [
                "id" => 18,
                "parent_id" => 0,
                "order" => 15,
                "title" => "回复管理",
                "icon" => "fa-comments",
                "uri" => "",
            ],
            [
                "id" => 19,
                "parent_id" => 18,
                "order" => 16,
                "title" => "回复列表",
                "icon" => "fa-commenting",
                "uri" => "comments",
            ],
            [
                "id" => 20,
                "parent_id" => 14,
                "order" => 12,
                "title" => "类型管理",
                "icon" => "fa-anchor",
                "uri" => "types",
            ],
            [
                "id" => 21,
                "parent_id" => 0,
                "order" => 17,
                "title" => "用户反馈",
                "icon" => "fa-sticky-note",
                "uri" => "",
            ],
            [
                "id" => 22,
                "parent_id" => 21,
                "order" => 18,
                "title" => "举报列表",
                "icon" => "fa-book",
                "uri" => "reports",
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
