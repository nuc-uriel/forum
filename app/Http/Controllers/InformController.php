<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/4/29 0029
 * Time: 1:40
 */

namespace App\Http\Controllers;

use App\Inform;
use App\Message;
use App\User;
use Illuminate\Http\Request;

class InformController extends Controller
{
    public function dispose(Request $request, $res, $code)
    {
        if (!in_array($res, array('pass', 'refuse'))) {
            result(10030, '链接已失效！');
        }
        $inform = Inform::where('code', $code)->whereIn('status', array(Inform::STATUS_UNREAD, Inform::STATUS_READ))->first();
        if (!$inform) {
            result(10030, '链接已失效！');
        }
        return $inform->dispose($request, $res == 'pass', Inform::STATUS_DENIED);
    }

    public function show(Request $request, $type)
    {
        if (!in_array($type, array('wait_pass', 'notify','reply','like','follow'))) {
            abort(404);
        }
        $user = User::find(session('uid'));
        $user->informs()->where('status', Inform::STATUS_UNREAD)->whereBetween('type', Inform::$type_info[$type]['range'])->update(array(
            'status'=>Inform::STATUS_READ
        ));
        $inform_count = $user->informs->where('status', Inform::STATUS_UNREAD)->count();
        $message_count = Message::where('ut_id', $user->id)->where('status', Message::STATUS_UNREAD)->count();
        return view('front.member.inform', array(
            'type_info'=>Inform::$type_info[$type],
            'type' => $type,
            'user' => $user,
            'message_count' => $message_count,
            'inform_count' => $inform_count
        ));
    }

    public function showList(Request $request)
    {
        $user = User::find(session('uid'));
        $inform_count = $user->informs->where('status', Inform::STATUS_UNREAD)->count();
        $message_count = Message::where('ut_id', $user->id)->where('status', Message::STATUS_UNREAD)->count();
        return view('front.member.chat_list', array(
            'user' => $user,
            'inform_count' => $inform_count,
            'message_count' => $message_count
        ));
    }

    public function del(Request $request, $type)
    {
        if (!in_array($type, array('wait_pass', 'notify','reply','like','follow'))) {
            abort(404);
        }
        $user = User::find(session('uid'));
        $user->informs()->whereBetween('type', Inform::$type_info[$type]['range'])->update(array(
            'status'=>Inform::STATUS_DELETED
        ));
        return result(10000, '删除成功！');
    }
}
