<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Apply;
use App\Admin\Extensions\Ban;
use App\Admin\Extensions\Lift;
use App\Admin\Extensions\ResetGroupAdmin;
use App\Admin\Extensions\Tools\BanOrLift;
use App\Group;

use App\GroupMember;
use App\GroupType;
use App\Inform;
use App\User;
use DB;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Form as NewForm;

class GroupController extends Controller
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

            $content->header('小组');
            $content->description('列表');
            $content->row('<meta id="reset-group-res" content="">');
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
        return Admin::grid(Group::class, function (Grid $grid) {
            $grid->disableCreation();
            $grid->model()->withCount('allMember as member')->withCount('topics')->where('status', '!=', Group::STATUS_DELETED)->orderBy(\DB::raw('field(`status`, 1,0,3,2)'));
            $grid->id('ID')->sortable();
            $grid->column('name', '名称');
            $grid->type()->name('类型');
            $grid->leader('组长')->display(function ($leader) {
                return "<a href='javascript:void(0);' class='grid-show-row' data-id='{$leader[0]['id']}' data-type='users'>{$leader[0]['username']}</a>";
            });
            $grid->column('introduce', '介绍')->limit(30);
            $grid->column('member_count', '成员')->sortable();
            $grid->column('topics_count', '主题')->display(function ($topics_count) {
                return "<a href='/admin/topics?g_id={$this->id}'>$topics_count</a>";
            })->sortable();
            $grid->status('状态')->display(function ($status) {
                if ($status == Group::STATUS_WAIT_CONFIRM) {
                    return "<span class='label bg-lime'>待审核</span>";
                } elseif ($status == Group::STATUS_NOT_PASS) {
                    return "<span class='label label-default'>审核未通过</span>";
                } elseif ($status == Group::STATUS_BANNED) {
                    return "<span class='label label-warning'>封禁</span>";
                } else {
                    return "<span class='label label-success'>正常</span>";
                }
            });
            $grid->created_at('创建时间')->sortable();
            $grid->filter(function ($filter) {
                $filter->like('name', '名称');
                $filter->is('status', '状态')->select(function () {
                    return array(
                        Group::STATUS_NORMAL => '正常',
                        Group::STATUS_BANNED => '封禁',
                        Group::STATUS_WAIT_CONFIRM => '待审核',
                        Group::STATUS_NOT_PASS => '审核未通过',
                    );
                });
                $filter->is('gt_id', '类型')->select(function () {
                    return GroupType::where('status', GroupType::STATUS_NORMAL)->get(['id', 'name'])->keyBy('id')->map(function ($item) {
                        return $item->name;
                    })->toArray();
                });
            });
            $grid->actions(function ($actions) {
                $group = $actions->row;
                if ($group->status == Group::STATUS_WAIT_CONFIRM) {
                    $actions->prepend(new Apply($actions->getKey(), 'refuse'));
                    $actions->prepend(new Apply($actions->getKey(), 'pass'));
                    $actions->disableDelete();
                } elseif ($group->status == Group::STATUS_NORMAL) {
                    $actions->prepend(new Ban($actions->getKey(), 'groups'));
                    $actions->prepend(new ResetGroupAdmin($actions->getKey()));
                } else {
                    $actions->prepend(new Lift($actions->getKey(), 'groups'));
                    $actions->prepend(new ResetGroupAdmin($actions->getKey()));
                }
                $actions->prepend("<a href='javascript:void(0);' class='grid-show-row' title='查看小组' data-id='{$actions->getKey()}' data-type='groups'><i class='fa fa-eye'></i></a>");
                $actions->disableEdit();
            });
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->add('封禁', new BanOrLift('groups', 'ban'));
                    $batch->add('解封', new BanOrLift('groups', 'lift'));
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
        return Admin::form(Group::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function destroy($id)
    {
        if (Admin::user()->can('group_sort_del')) {
            $group = Group::find($id);
            $group->status = Group::STATUS_DELETED;
            if ($group->save()) {
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
        if (Admin::user()->can('group_ban')) {
            $status = $request->input('status') == 'lift' ? Group::STATUS_NORMAL : Group::STATUS_BANNED;
            if ($request->isMethod('post')) {
                $ids = $request->input('ids');
                $res = Group::whereIn('id', $ids)->update(array(
                    'status' => $status
                ));
            } else {
                $group = Group::find($request->input('id'));
                if (!$group) {
                    return response()->json([
                        'status' => false,
                        'message' => trans('小组不存在！'),
                    ]);
                }
                $group->status = $status;
                $res = $group->save();
            }
            if ($res) {
                return response()->json([
                    'status' => true,
                    'message' => trans($status == Group::STATUS_NORMAL ? '解封成功！' : '封禁成功！'),
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => trans($status == Group::STATUS_NORMAL ? '解封失败！' : '封禁失败！'),
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
        $group = Group::find($id);
        return Admin::content(function (Content $content) use ($group) {

            $content->header('小组');
            $content->description('详情');
//            $content->type('iframe');

            $headers = ['', ''];
            $rows = [
                '图标' => "<img src='{$group->icon}' class='img-md'/>",
                '名称' => $group->name,
                '类型' => $group->type->name,
                '组长' => "<a href='/member?uid={$group->leader()->first()->id}' target='_blank'>{$group->leader()->first()->username}</a>",
                '管理员别称' => $group->admin_as,
                '成员别称' => $group->member_as,
                '加入方式' => $group->join_way == Group::JOIN_WAY_ALLOW ? "允许任何人加入" : ($group->join_way == Group::JOIN_WAY_APPLY ? "需要申请" : "不允许任何人加入"),
                '介绍' => $group->introduce,
                '创建时间' => $group->created_at,
                '主页' => "<a href='/group/{$group->id}' target='_blank'>" . env('APP_URL') . "group/{$group->id}</a>",
            ];
            $table = new Table($headers, $rows);
            $content->body((new Box('小组信息', $table))->style('info')->solid());
        });
    }

    public function apply(Request $request)
    {
        if (Admin::user()->can('group_apply')) {
            $status = $request->input('status') == 'pass' ? Group::STATUS_NORMAL : Group::STATUS_NOT_PASS;
            $group = Group::find($request->input('id'));
            if (!$group) {
                return response()->json([
                    'status' => false,
                    'message' => trans('小组不存在！'),
                ]);
            }
            $group->status = $status;
            DB::beginTransaction();
            try {
                $group->save();
                $inform = new Inform();
                $inform->setTo($group->u_id)->sendInform($group, 211, array(
                    'g_id' => $group->id,
                    'g_name' => $group->name,
                    'res' => $status == Group::STATUS_NORMAL ? '已通过审核' : '没有通过审核'
                ))->setFrom(0)->save();
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => trans($status == Group::STATUS_NORMAL ? '通过成功！' : '拒绝成功！'),
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => trans($status == Group::STATUS_NORMAL ? '通过失败！' : '拒绝失败！'),
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => trans('无操作权限！'),
            ]);
        }
    }

    public function reset(Request $request, $id)
    {
        $group = Group::find($id);
        if ($request->isMethod('get')) {
            return Admin::content(function (Content $content) use ($group) {
                $content->header('小组');
                $content->description('撤销组长');
//                $content->type('iframe');
                $form = new NewForm();
                $form->action('');
                $form->text('', '原组长')->default($group->leader()->first()->username)->attribute(['disabled' => 'true']);
                $form->select('uid', '新组长')->options($group->admin->pluck('username', 'id'));
                $form->disablePjax();
                $form->attribute(array(
                    'id'=>'group-reset'
                ));
                $content->body((new Box('撤销组长', $form))->style('info')->solid());
            });
        } else {
            if (Admin::user()->can('group_reset')) {
                DB::beginTransaction();
                try {
                    $my = GroupMember::find($group->leader()->first()->pivot->id);
                    $my->role = GroupMember::ROLE_ADMIN;
                    $my->timestamps = false;
                    $my->save();
                    $gm = GroupMember::find($group->admin->find($request->input('uid'))->pivot->id);
                    $gm->role = GroupMember::ROLE_LEADER;
                    $gm->timestamps = false;
                    $gm->save();
                    $inform = new Inform();
                    $inform->setTo($request->input('uid'))->sendInform($gm, 210, array(
                        'g_id' => $group->id,
                        'g_name' => $group->name,
                    ))->setFrom(0)->save();
                    DB::commit();
                    return response()->json([
                        'status' => true,
                        'message' => trans('操作成功！'),
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => trans('操作失败！'),
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => trans('无操作权限！'),
                ]);
            }
        }
    }
}
