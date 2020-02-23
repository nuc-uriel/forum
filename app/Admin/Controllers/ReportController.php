<?php

namespace App\Admin\Controllers;

use App\Comment;
use App\Group;
use App\Report;

use App\Topic;
use App\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use function foo\func;
use Illuminate\Http\Request;

class ReportController extends Controller
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

            $content->header('举报');
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
        return Admin::grid(Report::class, function (Grid $grid) {
            $grid->model()->where('status', '!=', Report::STATUS_DELETED);
            $grid->disableCreation();
            $grid->id('ID')->sortable();
            $grid->u_id('举报人')->display(function ($uid) {
                if ($uid == 0) {
                    return '匿名';
                } else {
                    return "<a href='javascript:void(0);' class='grid-show-row' data-id='{$uid}' data-type='users'>{$this->creator->username}</a>";
                }
            });
            $grid->column('type', '类型')->display(function ($type) {
                switch ($type) {
                    case Report::TYPE_USER:
                        return '用户';
                        break;
                    case Report::TYPE_GROUP:
                        return '小组';
                        break;
                    case Report::TYPE_TOPIC:
                        return '讨论';
                        break;
                    case Report::TYPE_COMMENT:
                        return '回复';
                        break;
                    default:
                        return '未知类型';
                        break;
                }
            });
            $grid->column('target_id', '目标')->display(function ($target_id) {
                switch ($this->type) {
                    case Report::TYPE_USER:
                        $user = User::find($target_id);
                        if ($user) {
                            return "<a href='javascript:void(0);' class='grid-show-row' data-id='{$user->id}' data-type='users'>".str_limit($user->username, 30)."</a>";
                        } else {
                            return "未知目标";
                        }
                        break;
                    case Report::TYPE_GROUP:
                        $group = Group::find($target_id);
                        if ($group) {
                            return "<a href='javascript:void(0);' class='grid-show-row' data-id='{$group->id}' data-type='groups'>".str_limit($group->name, 30)."</a>";
                        } else {
                            return "未知目标";
                        }
                        break;
                    case Report::TYPE_TOPIC:
                        $topic = Topic::find($target_id);
                        if ($topic) {
                            return "<a href='/topic/{$topic->id}' target='_blank'>".str_limit($topic->title, 30)."</a>";
                        } else {
                            return "未知目标";
                        }
                        break;
                    case Report::TYPE_COMMENT:
                        $comment = Comment::find($target_id);
                        if ($comment) {
                            return "<a href='/topic/{$comment->topic->id}' target='_blank'>".str_limit($comment->topic->title, 30)."</a>";
                        } else {
                            return "未知目标";
                        }
                        break;
                    default:
                        return '未知目标';
                        break;
                }
            });
            $grid->column('content', '内容')->limit(30);
            $grid->status('状态')->display(function ($status) {
                return "<span class='label label-success'>正常</span>";
            });
            $grid->created_at('创建时间')->sortable();
            $grid->filter(function ($filter) {
                $filter->like('content', '搜索');
                $filter->is('type', '类型')->select(function () {
                    return array(
                        Report::TYPE_USER=>'用户',
                        Report::TYPE_GROUP=>'小组',
                        Report::TYPE_TOPIC=>'讨论',
                        Report::TYPE_COMMENT=>'回复',
                    );
                });
            });
            $grid->actions(function ($actions) {
                $actions->prepend("<a href='javascript:void(0);' class='grid-show-row' title='查看详情' data-id='{$actions->getKey()}' data-type='reports'><i class='fa fa-eye'></i></a>");
                $actions->disableEdit();
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
        return Admin::form(Report::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function destroy($id)
    {
        if (Admin::user()->can('report_sort_del')) {
            $report = Report::find($id);
            $report->status = Report::STATUS_DELETED;
            if ($report->save()) {
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

    public function show(Request $request, $id)
    {
        $report = Report::find($id);
        return Admin::content(function (Content $content) use ($report) {
            if ($report->u_id == 0) {
                $creator =  '匿名';
            } else {
                $creator = "<a href='/member?uid={$report->u_id}' target='_blank'>{$report->creator->username}</a>";
            }
            switch ($report->type) {
                case Report::TYPE_USER:
                    $type = '用户';
                    $user = User::find($report->target_id);
                    if ($user) {
                        $target = "<a href='/member?uid={$user->id}' target='_blank'>{$user->username}</a>";
                    } else {
                        $target =  "未知目标";
                    }
                    break;
                case Report::TYPE_GROUP:
                    $type = '小组';
                    $group = Group::find($report->target_id);
                    if ($group) {
                        $target =  "<a href='/group/{$group->id}' target='_blank'>{$group->name}</a>";
                    } else {
                        $target =  "未知目标";
                    }
                    break;
                case Report::TYPE_TOPIC:
                    $type = '讨论';
                    $topic = Topic::find($report->target_id);
                    if ($topic) {
                        $target =  "<a href='/topic/{$topic->id}' target='_blank'>{$topic->title}</a>";
                    } else {
                        $target =  "未知目标";
                    }
                    break;
                case Report::TYPE_COMMENT:
                    $type = '回复';
                    $comment = Comment::find($report->target_id);
                    if ($comment) {
                        $target =  "<a href='/topic/{$comment->topic->id}' target='_blank'>{$comment->topic->title}</a>";
                    } else {
                        $target =  "未知目标";
                    }
                    break;
                default:
                    $type = '未知类型';
                    $target =  '未知目标';
                    break;
            }

            $content->header('举报');
            $content->description('详情');
//            $content->type('iframe');

            $headers = ['', ''];
            $rows = [
                '举报人' => $creator,
                '类型' => $type,
                '目标' => $target,
                '内容' => $report->content,
                '创建时间' => $report->created_at
            ];
            $table = new Table($headers, $rows);
            $content->body((new Box('举报信息', $table))->style('info')->solid());
        });
    }
}
