<?php


namespace App\Http\Controllers;

use App\Inform;
use App\User;
use App\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function show(Request $request, $uid)
    {
        $myself = User::find(session('uid'));
        $friend = User::find($uid);
        if ($friend && $myself->id != $friend->id) {
            $myself->getMessageForOne($friend->id);
            $inform_count = $myself->informs->where('status', Inform::STATUS_UNREAD)->count();
            $message_count = Message::where('ut_id', $myself->id)->where('status', Message::STATUS_UNREAD)->count();
            return view('front.member.chat', array(
                'friend' => $friend,
                'myself' => $myself,
                'message_count' => $message_count,
                'inform_count' => $inform_count
            ));
        } else {
            abort(404);
        }
    }

    public function showList(Request $request)
    {
        $user = User::find(session('uid'));
        $inform_count = $user->informs->where('status', Inform::STATUS_UNREAD)->count();
        $message_count = Message::where('ut_id', $user->id)->where('status', Message::STATUS_UNREAD)->count();
        $messages = $user->contacts()->paginate(8);
        $messages->map(function ($item, $key) use ($user) {
            $item->count = Message::where('ut_id', $user->id)->where('uf_id', $item->uf_id == session('uid') ? $item->receiver->id : $item->sender->id)->where('status', Message::STATUS_UNREAD)->count();
            return $item;
        });
        return view('front.member.chat_list', array(
            'user' => User::find(session('uid')),
            'messages' => $messages,
            'inform_count' => $inform_count,
            'message_count' => $message_count
        ));
    }

    public function send(Request $request)
    {
        $this->validate($request, [
            'uid' => 'required|exists:user,id,status,0',
            'content' => 'required|string',
        ], [
            'uid.required' => '用户不存在或账号未激活',
            'uid.exists' => '用户不存在或账号未激活',
            'content.required' => '消息不能为空',
            'content.string' => '消息错误'
        ]);
        $uf_id = session('uid');
        $user_from = User::find($uf_id);
        $ut_id = $request->input('uid');
        $user_to = User::find($ut_id);
        if ($uf_id == $ut_id) {
            return result(20033, '不能给自己发消息');
        }
        if ($user_from->blacklist->find($ut_id)) {
            return result(20031, '您已将对方加入黑名单，不可发送消息');
        }
        if ($user_to->blacklist->find($uf_id)) {
            return result(20032, '对方拒收消息');
        }
        $message = new Message();
        $message->uf_id = $uf_id;
        $message->ut_id = $ut_id;
        $message->content = $request->input('content');
        $message->save();
        return result(10000, '发送成功');
    }

    public function read(Request $request)
    {
        $mid = $request->input('mid');
        $msg = Message::find($mid);
        if ($msg->receiver->id == session('uid')) {
            $msg->status = Message::STATUS_READ;
            $msg->save();
        }
        return result(10000);
    }

    public function del(Request $request, $uid)
    {
        $myself = User::find(session('uid'));
        $friend = User::find($uid);
        if (!$friend) {
            return result(20008, '用户不存在！');
        }
        $code = Message::getGroupCode($myself->id, $friend->id);
        \DB::beginTransaction();
        try {
            Message::where('ut_id', $myself->id)->where('group_code', $code)->update(array('status' => Message::STATUS_RECEIVER_DELETED));
            Message::where('uf_id', $myself->id)->where('group_code', $code)->update(array('status' => Message::STATUS_SENDER_DELETED));
            \DB::commit();
            return result(10000, '删除成功！');
        } catch (\Exception $e) {
            \DB::rollBack();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }
}
