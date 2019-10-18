<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\BanOrLift;
use App\Admin\Extensions\Ban;
use App\Admin\Extensions\Lift;
use App\User;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('用户');
            $content->description('列表');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(User::class, function (Grid $grid) {
            $grid->disableCreation();
            $grid->model()->whereIn('status', array(User::STATUS_NORMAL, User::STATUS_BANNED));
            $grid->id('ID')->sortable();
            $grid->column('username', '昵称');
            $grid->sex('性别')->display(function ($sex) {
                return $sex == 0 ? "保密" : ($sex == 1 ? "男" : "女");
            });
            $grid->place('居住地')->display(function ($place) {
                return empty($place) ? "保密" : $place;
            });
            $grid->email('邮箱');
            $grid->confirmation('激活')->display(function ($confirmation) {
                if (empty($place)) {
                    return "<span class='label label-success'>已激活</span>";
                } else {
                    return "<span class='label label-warning'>未激活</span>";
                }
            });
            $grid->status('状态')->display(function ($status) {
                if ($status == User::STATUS_BANNED) {
                    return "<span class='label label-warning'>封禁</span>";
                } else {
                    return "<span class='label label-success'>正常</span>";
                }
            });
            $grid->created_at('加入时间')->sortable();
//            $grid->updated_at('更新时间')->sortable();

            $grid->filter(function ($filter) {
                $filter->like('username', '昵称');
                $filter->is('status', '状态')->select(function () {
                    return array(
                        User::STATUS_NORMAL => '正常',
                        User::STATUS_BANNED => '封禁',
                    );
                });
            });
            $grid->actions(function ($actions) {
                $user = $actions->row;
                if ($user->status == User::STATUS_NORMAL) {
                    $actions->prepend(new Ban($actions->getKey(), 'users'));
                } else {
                    $actions->prepend(new Lift($actions->getKey(), 'users'));
                }
                $actions->prepend("<a href='javascript:void(0);' class='grid-show-row' title='查看用户' data-id='{$actions->getKey()}' data-type='users'><i class='fa fa-eye'></i></a>");
                $actions->disableEdit();
            });
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->add('封禁', new BanOrLift('users', 'ban'));
                    $batch->add('解封', new BanOrLift('users', 'lift'));
                });
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(User::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function destroy($id)
    {
        if (Admin::user()->can('user_sort_del')) {
            $user = User::find($id);
            $user->status = User::STATUS_DELETED;
            if ($user->save()) {
                return response()->json([
                    'status' => true,
                    'message' => trans('admin::lang.delete_succeeded'),
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => trans('admin::lang.delete_failed'),
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => trans('无操作权限！'),
            ]);
        }
    }

    public function ban(Request $request)
    {
        if (Admin::user()->can('user_ban')) {
            $status = $request->input('status') == 'lift' ? User::STATUS_NORMAL : User::STATUS_BANNED;
            if ($request->isMethod('post')) {
                $ids = $request->input('ids');
                $res = User::whereIn('id', $ids)->update(array(
                    'status' => $status
                ));
            } else {
                $user = User::find($request->input('id'));
                if (!$user) {
                    return response()->json([
                        'status' => false,
                        'message' => trans('用户不存在！'),
                    ]);
                }
                $user->status = $status;
                $res = $user->save();
            }
            if ($res) {
                return response()->json([
                    'status' => true,
                    'message' => trans($status == User::STATUS_NORMAL ? '解封成功！' : '封禁成功！'),
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => trans($status == User::STATUS_NORMAL ? '解封失败！' : '封禁失败！'),
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => trans('无操作权限！'),
            ]);
        }
    }

    public function show(Request $request, $id)
    {
        $user = User::find($id);
        return Admin::content(function (Content $content) use ($user) {

            $content->header('用户');
            $content->description('详情');
            $content->type('iframe');

            $headers = ['', ''];
            $rows = [
                '头像' => "<img src='{$user->avatar}' class='img-md'/>",
                '昵称' => $user->username,
                '性别' => $user->sex == 0 ? "保密" : ($user->sex == 1 ? "男" : "女"),
                '年龄' => $user->age,
                '居住地' => $user->place,
                '邮箱' => $user->email,
                '签名' => $user->signature,
                '介绍' => $user->introduce,
                '注册时间' => $user->created_at,
                '主页' => "<a href='/member?uid={$user->id}' target='_blank'>" . env('APP_URL') . "member?uid={$user->id}</a>",
            ];
            $table = new Table($headers, $rows);
            $content->body((new Box('用户信息', $table))->style('info')->solid());
        });
    }
}
