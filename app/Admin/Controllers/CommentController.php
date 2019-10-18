<?php

namespace App\Admin\Controllers;

use App\Comment;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class CommentController extends Controller
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

            $content->header('回复');
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
        return Admin::grid(Comment::class, function (Grid $grid) {
            $grid->disableCreation();
            $grid->model()->where('status', '!=', Comment::STATUS_DELETED);
            $grid->id('ID')->sortable();
            $grid->topic('主题')->display(function ($topic) {
                return "<a href='/topic/{$topic['id']}' title='{$topic['title']}'>".str_limit($topic['title'], 30)."</a>";
            });
            $grid->column('content', '内容')->limit(30);
            $grid->creator('评论者')->display(function ($creator) {
                return "<a href='javascript:void(0);' class='grid-show-row' data-id='{$creator['id']}' data-type='users'>{$creator['username']}</a>";
            });
            $grid->status('状态')->display(function ($status) {
                return "<span class='label label-success'>正常</span>";
            });
            $grid->created_at('创建时间')->sortable();
            $grid->filter(function ($filter) {
                $filter->like('content', '搜索');
                $filter->is('t_id', '主题ID');
            });
            $grid->actions(function ($actions) {
                $comment = $actions->row;
                $actions->prepend("<a href='/topic/{$comment->t_id}' title='查看讨论'><i class='fa fa-eye'></i></a>");
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
        return Admin::form(Comment::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function destroy($id)
    {
        if (Admin::user()->can('comment_sort_del')) {
            $topic = Comment::find($id);
            $topic->status = Comment::STATUS_DELETED;
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
}
