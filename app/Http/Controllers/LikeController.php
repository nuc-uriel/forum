<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/5/7 0007 0024
 * Time: 20:30
 */

namespace App\Http\Controllers;

use App\Comment;
use App\Inform;
use App\Like;
use App\Topic;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * @function 点赞
     * @param Request $request
     * @param $target
     * @return array
     */
    public function add(Request $request, $target)
    {
        $this->validate($request, array(
            'type' => "required|in:" . Like::TYPE_COMMENT . ',' . Like::TYPE_TOPIC,
        ), array(
            'type.required' => '类型缺失',
            'type.in' => '类型错误',
        ));
        $type = $request->input('type');
        if ($type == Like::TYPE_COMMENT) {
            $comment = Comment::where('status', Comment::STATUS_NORMAL)->find($target);
            if (!$comment) {
                return result(20038, '您点赞的内容不存在，请刷新页面再试！');
            }
            $topic = $comment->topic;
        } else {
            $topic = Topic::find($target);
        }
        if (!$topic || $topic->status != Topic::STATUS_NORMAL) {
            return result(20037, '您点赞的讨论不存在，请刷新页面再试！');
        }
        \DB::beginTransaction();
        try {
            $like = Like::updateOrCreate(array(
                'type' => $type,
                'target_id' => $target,
                'u_id' => session('uid'),
            ), array(
                'status' => Like::STATUS_NORMAL,
            ));
            $inform = new Inform();
            $inform->setTo($type == Like::TYPE_COMMENT ? $comment->u_id : $topic->u_id)->sendInform($like, $type == Like::TYPE_COMMENT ? 401 : 400, array(
                'u_id' => session('uid'),
                'u_name' => session('uname'),
                't_id' => $topic->id,
                'title' => $topic->title
            ))->save();
            \DB::commit();
            return result(10000, '点赞成功！');
        } catch (\Exception $e) {
            \DB::rollBack();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 取消点赞
     * @param Request $request
     * @param $target
     * @return array
     */
    public function del(Request $request, $target)
    {
        $this->validate($request, array(
            'type' => "required|in:" . Like::TYPE_COMMENT . ',' . Like::TYPE_TOPIC,
        ), array(
            'type.required' => '类型缺失',
            'type.in' => '类型错误',
        ));
        $type = $request->input('type');
        $like = Like::where(array(
            'u_id' => session('uid'),
            'type' => $type,
            'target_id' => $target,
            'status' => Like::STATUS_NORMAL
        ))->first();
        if (!$like) {
            return result(20011, '无操作权限！');
        }
        $like->status = Like::STATUS_DELETED;
        $like->save();
        return result(10000, '取消成功！');
    }
}
