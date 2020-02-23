<?php

namespace App\Admin\Controllers;

use App\Comment;
use App\Group;
use App\Http\Controllers\Controller;
use App\Report;
use App\Topic;
use App\User;
use DB;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Chart\Bar;
use Encore\Admin\Widgets\Chart\Doughnut;
use Encore\Admin\Widgets\Chart\Line;
use Encore\Admin\Widgets\Chart\Pie;
use Encore\Admin\Widgets\Chart\PolarArea;
use Encore\Admin\Widgets\Chart\Radar;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;

class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('首页');
            $content->description('数据统计');

            $content->row(function ($row) {
                $row->column(6, new InfoBox('用户', 'users', 'aqua', '/admin/users', User::where('status', User::STATUS_NORMAL)->count()));
                $row->column(6, new InfoBox('小组', 'object-group', 'green', '/admin/groups', Group::where('status', Group::STATUS_NORMAL)->count()));
            });
            $content->row(function ($row) {
                $row->column(6, new InfoBox('讨论', 'file-powerpoint-o', 'yellow', '/admin/topics', Topic::where('status', Topic::STATUS_NORMAL)->count()));
                $row->column(6, new InfoBox('回复', 'comments', 'red', '/admin/comments', Comment::where('status', Comment::STATUS_NORMAL)->count()));
            });
            $content->row(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->append((new Box('月内用户增长表', new Line($this->getChart('member'))))->removable()->collapsable()->style('danger'));
                });
            });
            $content->row(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->append((new Box('月内小组增长表', new Line($this->getChart('group'))))->removable()->collapsable()->style('danger'));
                });
            });
            $content->row(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->append((new Box('月内讨论增长表', new Line($this->getChart('topic'))))->removable()->collapsable()->style('danger'));
                });
            });
            $content->row(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->append((new Box('月内评论增长表', new Line($this->getChart('comment'))))->removable()->collapsable()->style('danger'));
                });
            });
            $content->row(function (Row $row) {
                $row->column(6, function (Column $column) {
                    $column->append((new Box('月内投诉比例表', new Pie($this->getReportChart())))->removable()->collapsable()->style('danger'));
                });
//                $row->column(6, function (Column $column) {
//                    $column->append((new Box('月内投诉比例表', new Pie($this->getGroupChart())))->removable()->collapsable()->style('danger'));
//                });
            });

//
//            $content->row(function (Row $row) {
//
//                $row->column(6, function (Column $column) {
//                    $tab = new Tab();
//
//                    $pie = new Pie([
//                        ['Stracke Ltd', 450], ['Halvorson PLC', 650], ['Dicki-Braun', 250], ['Russel-Blanda', 300],
//                        ['Emmerich-O\'Keefe', 400], ['Bauch Inc', 200], ['Leannon and Sons', 250], ['Gibson LLC', 250],
//                    ]);
//
//                    $tab->add('Pie', $pie);
//                    $tab->add('Table', new Table());
//                    $tab->add('Text', 'blablablabla....');
//
//                    $tab->dropDown([['Orders', '/admin/orders'], ['administrators', '/admin/administrators']]);
//                    $tab->title('Tabs');
//
//                    $column->append($tab);
//
//                    $collapse = new Collapse();
//
//                    $bar = new Bar(
//                        ["January", "February", "March", "April", "May", "June", "July"],
//                        [
//                            ['First', [40,56,67,23,10,45,78]],
//                            ['Second', [93,23,12,23,75,21,88]],
//                            ['Third', [33,82,34,56,87,12,56]],
//                            ['Forth', [34,25,67,12,48,91,16]],
//                        ]
//                    );
//                    $collapse->add('Bar', $bar);
//                    $collapse->add('Orders', new Table());
//                    $column->append($collapse);
//
//                    $doughnut = new Doughnut([
//                        ['Chrome', 700],
//                        ['IE', 500],
//                        ['FireFox', 400],
//                        ['Safari', 600],
//                        ['Opera', 300],
//                        ['Navigator', 100],
//                    ]);
//                    $column->append((new Box('Doughnut', $doughnut))->removable()->collapsable()->style('info'));
//                });
//
//                $row->column(6, function (Column $column) {
//
//                    $column->append(new Box('Radar', new Radar()));
//
//                    $polarArea = new PolarArea([
//                        ['Red', 300],
//                        ['Blue', 450],
//                        ['Green', 700],
//                        ['Yellow', 280],
//                        ['Black', 425],
//                        ['Gray', 1000],
//                    ]);
//                    $column->append((new Box('Polar Area', $polarArea))->removable()->collapsable());
//
//
//                });
//
//            });
//
//            $headers = ['Id', 'Email', 'Name', 'Company', 'Last Login', 'Status'];
//            $rows = [
//                [1, 'labore21@yahoo.com', 'Ms. Clotilde Gibson', 'Goodwin-Watsica', '1997-08-13 13:59:21', 'open'],
//                [2, 'omnis.in@hotmail.com', 'Allie Kuhic', 'Murphy, Koepp and Morar', '1988-07-19 03:19:08', 'blocked'],
//                [3, 'quia65@hotmail.com', 'Prof. Drew Heller', 'Kihn LLC', '1978-06-19 11:12:57', 'blocked'],
//                [4, 'xet@yahoo.com', 'William Koss', 'Becker-Raynor', '1988-09-07 23:57:45', 'open'],
//                [5, 'ipsa.aut@gmail.com', 'Ms. Antonietta Kozey Jr.', 'Braun Ltd', '2013-10-16 10:00:01', 'open'],
//            ];
//
//            $content->row((new Box('Table', new Table($headers, $rows)))->style('info')->solid());
        });
    }

    private function getChart($chart)
    {
        $start_time = strtotime(date('Y-m-d', strtotime("-1 month"))) + 86400;
        switch ($chart) {
            case 'member':
                $res = User::where('created_at', '>=', $start_time)->where('status', User::STATUS_NORMAL)->get()->groupBy(function ($item, $key) {
                    return strtotime($item->created_at->format('Y-m-d'));
                })->map(function ($item, $key) {
                    return $item->count();
                });
                break;
            case 'group':
                $res = Group::where('created_at', '>=', $start_time)->where('status', Group::STATUS_NORMAL)->get()->groupBy(function ($item, $key) {
                    return strtotime($item->created_at->format('Y-m-d'));
                })->map(function ($item, $key) {
                    return $item->count();
                });
                break;
            case 'topic':
                $res = Topic::where('created_at', '>=', $start_time)->where('status', Topic::STATUS_NORMAL)->get()->groupBy(function ($item, $key) {
                    return strtotime($item->created_at->format('Y-m-d'));
                })->map(function ($item, $key) {
                    return $item->count();
                });
                break;
            case 'comment':
                $res = Comment::where('created_at', '>=', $start_time)->where('status', Comment::STATUS_NORMAL)->get()->groupBy(function ($item, $key) {
                    return strtotime($item->created_at->format('Y-m-d'));
                })->map(function ($item, $key) {
                    return $item->count();
                });
                break;
        }
        $end_time = time();
        for (; $start_time < $end_time; $start_time += 86400) {
            $res->has($start_time) or $res[$start_time] = 0;
        }
        return $res;
    }

    private function getReportChart()
    {
        $start_time = strtotime(date('Y-m-d', strtotime("-1 month"))) + 86400;
        $res = Report::where('created_at', '>=', $start_time)->where('status', Report::STATUS_NORMAL)->groupBy('type')->select(DB::raw('count(*) as count, type'))->get()->pluck('count', 'type')->toArray();
        $data = [];
        $data[] = ['用户', isset($res[Report::TYPE_USER]) ? $res[Report::TYPE_USER] : 0];
        $data[] = ['小组', isset($res[Report::TYPE_GROUP]) ? $res[Report::TYPE_GROUP] : 0];
        $data[] = ['讨论', isset($res[Report::TYPE_TOPIC]) ? $res[Report::TYPE_TOPIC] : 0];
        $data[] = ['评论', isset($res[Report::TYPE_COMMENT]) ? $res[Report::TYPE_COMMENT] : 0];
        return $data;
    }

    private function getGroupChart()
    {
        $start_time = strtotime(date('Y-m-d', strtotime("-1 month"))) + 86400;
        $res = Group::where('created_at', '>=', $start_time)->where('status', Report::STATUS_NORMAL)->withCount('comments')->orderByDesc('comments_count')->take(5)->get(array('name', 'comments_count'));
        $data = [];
        $data[] = ['用户', isset($res[Report::TYPE_USER]) ? $res[Report::TYPE_USER] : 0];
        $data[] = ['小组', isset($res[Report::TYPE_GROUP]) ? $res[Report::TYPE_GROUP] : 0];
        $data[] = ['讨论', isset($res[Report::TYPE_TOPIC]) ? $res[Report::TYPE_TOPIC] : 0];
        $data[] = ['评论', isset($res[Report::TYPE_COMMENT]) ? $res[Report::TYPE_COMMENT] : 0];
        return $data;
    }
}
