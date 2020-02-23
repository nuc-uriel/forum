<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/5/12 0012 0024
 * Time: 21:36
 */

namespace App\Http\Controllers;

use App\Comment;
use App\Group;
use App\Report;
use App\Topic;
use App\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * @function 保存举报信息
     * @param Request $request
     * @return array
     */
    public function report(Request $request)
    {
        switch (intval($request->input('type'))) {
            case Report::TYPE_USER:
                if (!User::find($request->input('target'))) {
                    return result(20040, '用户不存在！');
                }
                break;
            case Report::TYPE_GROUP:
                if (!Group::find($request->input('target'))) {
                    return result(20040, '小组不存在！');
                }
                break;
            case Report::TYPE_TOPIC:
                if (!Topic::find($request->input('target'))) {
                    return result(20040, '讨论不存在！');
                }
                break;
            case Report::TYPE_COMMENT:
                if (!Comment::find($request->input('target'))) {
                    return result(20040, '评论不存在！');
                }
                break;
            default:
                return result(20040, '目标不存在！');
                break;
        }
        $report = new Report();
        $report->u_id = session('uid', 0);
        $report->type = $request->input('type');
        $report->target_id = $request->input('target');
        $report->status = Report::STATUS_NORMAL;
        $report->content = $request->input('content');
        if ($report->save()) {
            return result(10000, '举报成功，管理员会稍后处理！');
        } else {
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }
}
