<?php

namespace App\Admin\Controllers;

use App\GroupType;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class GroupTypeController extends Controller
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

            $content->header('小组类型');
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

            $content->header('小组类型');
            $content->description('编辑');

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

            $content->header('小组类型');
            $content->description('创建');

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
        return Admin::grid(GroupType::class, function (Grid $grid) {
            $grid->model()->withCount('groups')->where('status', '!=', GroupType::STATUS_DELETED);
            $grid->id('ID')->sortable();
            $grid->column('name', '名称');
            $grid->column('groups_count', '小组数量')->display(function ($count) {
                return "<a href='/admin/groups?gt_id={$this->id}' title='查看小组'>{$count}</a>";
            })->sortable();
            $grid->status('状态')->display(function ($status) {
                return "<span class='label label-success'>正常</span>";
            });
            $grid->created_at('创建时间')->sortable();
            $grid->filter(function ($filter) {
                $filter->like('name', '搜索');
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
        return Admin::form(GroupType::class, function (Form $form) {

            $form->text('name', '名称');
        });
    }
}
