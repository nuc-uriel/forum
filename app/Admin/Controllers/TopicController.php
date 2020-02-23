<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Ban;
use App\Admin\Extensions\Lift;
use App\Admin\Extensions\Tools\BanOrLift;
use App\Group;
use App\Topic;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;

class TopicController extends Controller
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

            $content->header('讨论');
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
        return Admin::grid(Topic::class, function (Grid $grid) {
            $grid->disableCreation();
            $grid->model()->withCount('comments')->where('status', '!=', Topic::STATUS_DELETED);
            $grid->id('ID')->sortable();
            $grid->column('title', '标题')->limit(30);
            $grid->column('content', '内容')->display(function ($content) {
                return strip_tags($content);
            })->limit(30);
            $grid->creator('作者')->display(function ($creator) {
                return "<a href='javascript:void(0);' class='grid-show-row' data-id='{$creator['id']}' data-type='users'>{$creator['username']}</a>";
            });
            $grid->group('小组')->display(function ($group) {
                return "<a href='javascript:void(0);' class='grid-show-row' data-id='{$group['id']}' data-type='groups'>{$group['name']}</a>";
            });
            $grid->is_top('置顶')->switch([
                'on'  => ['value' => Topic::IS_TOP_TRUE, 'text' => '是', 'color' => 'primary'],
                'off' => ['value' => Topic::IS_TOP_FALSE, 'text' => '否', 'color' => 'default'],
            ]);
            $grid->can_comment('可以评论')->switch([
                'on'  => ['value' => Topic::CAN_COMMENT_TRUE, 'text' => '是', 'color' => 'primary'],
                'off' => ['value' => Topic::CAN_COMMENT_FALSE, 'text' => '否', 'color' => 'default'],
            ]);
            $grid->comments('评论数')->display(function ($comments) {
                return count($comments);
            })->sortable();
            $grid->status('状态')->display(function ($status) {
                if ($status == Topic::STATUS_BAN) {
                    return "<span class='label label-warning'>封禁</span>";
                } else {
                    return "<span class='label label-success'>正常</span>";
                }
            });
            $grid->created_at('创建时间')->sortable();
            $grid->filter(function ($filter) {
                $filter->where(function ($query) {
                    $query->where('title', 'like', "%{$this->input}%")
                        ->orWhere('content', 'like', "%{$this->input}%");
                }, '搜索');
                $filter->is('status', '状态')->select(function () {
                    return array(
                        Topic::STATUS_NORMAL => '正常',
                        Topic::STATUS_BAN => '封禁',
                    );
                });
                $filter->is('g_id', '小组')->select(Group::all()->pluck('name', 'id'));
            });
            $grid->actions(function ($actions) {
                $topic = $actions->row;
                if ($topic->status == Group::STATUS_NORMAL) {
                    $actions->prepend(new Ban($actions->getKey(), 'topics'));
                } else {
                    $actions->prepend(new Lift($actions->getKey(), 'topics'));
                }
                $actions->prepend("<a target='_blank' href='/topic/{$topic->id}' title='查看讨论'><i class='fa fa-eye'></i></a>");
                $actions->disableEdit();
            });
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->add('封禁', new BanOrLift('topics', 'ban'));
                    $batch->add('解封', new BanOrLift('topics', 'lift'));
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
        return Admin::form(Topic::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->switch('is_top', '置顶')->states([
                'on'  => ['value' => Topic::IS_TOP_TRUE, 'text' => '是', 'color' => 'primary'],
                'off' => ['value' => Topic::IS_TOP_FALSE, 'text' => '否', 'color' => 'default'],
            ]);
            $form->switch('can_comment', '可以评论')->states([
                'on'  => ['value' => Topic::CAN_COMMENT_TRUE, 'text' => '是', 'color' => 'primary'],
                'off' => ['value' => Topic::CAN_COMMENT_FALSE, 'text' => '否', 'color' => 'default'],
            ]);
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function destroy($id)
    {
        if (Admin::user()->can('topic_sort_del')) {
            $topic = Topic::find($id);
            $topic->status = Topic::STATUS_DELETED;
            if ($topic->save()) {
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
        if (Admin::user()->can('topic_ban')) {
            $status = $request->input('status') == 'lift' ? Topic::STATUS_NORMAL : Topic::STATUS_BAN;
            if ($request->isMethod('post')) {
                $ids = $request->input('ids');
                $res = Topic::whereIn('id', $ids)->update(array(
                    'status' => $status
                ));
            } else {
                $topic = Topic::find($request->input('id'));
                if (!$topic) {
                    return response()->json([
                        'status' => false,
                        'message' => trans('讨论不存在！'),
                    ]);
                }
                $topic->status = $status;
                $res = $topic->save();
            }
            if ($res) {
                return response()->json([
                    'status' => true,
                    'message' => trans($status == Topic::STATUS_NORMAL ? '解封成功！' : '封禁成功！'),
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => trans($status == Topic::STATUS_NORMAL ? '解封失败！' : '封禁失败！'),
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
