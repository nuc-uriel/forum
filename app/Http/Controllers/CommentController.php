<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/5/7 0007 0024
 * Time: 16:47
 */

namespace App\Http\Controllers;

use App\Comment;
use App\Group;
use App\Inform;
use App\Topic;
use App\User;
use Illuminate\Http\Request;
use Storage;

class CommentController extends Controller
{
//    /**
//     * @function 上传评论图片
//     * @param Request $request
//     * @return array
//     */
//    public function setImage(Request $request)
//    {
//        $this->validate($request, array(
//            'img' => 'required|mimes:jpeg,png,gif|max:5120',
//        ), array(
//            'required' => '必须传入:attribute',
//            'mimes' => ':attribute类型不允许,请上传常规的图片(jpg、png、gif)文件',
//            'max' => ':attribute过大,:attribute大小不得超出5MB',
//        ), array(
//            'img' => '图片'
//        ));
//        $image = $request->file('img');
//        $realPath = $image->getRealPath();
//        $hash = md5_file($realPath);
//        $savePath = 'temp/comment';
//        is_dir($savePath) || mkdir($savePath);
//        // 扩展名
//        $ext = $image->getClientOriginalExtension();
//        $img_name = $hash . '.' . $ext;
//        $fullname = '/' . $savePath . '/' . $img_name;
//        file_exists($savePath . '/' . $img_name) or $image->storeAs($savePath, $img_name, 'addressable');
//        return result('10000', $fullname);
//    }

    /**
     * @function 添加评论
     * @param Request $request
     * @return array
     */
    public function add(Request $request)
    {
        $this->validate($request, array(
            'type' => "required|in:" . Comment::TYPE_COMMENT . ',' . Comment::TYPE_REPLY,
            'target' => 'required',
            'content' => 'required',
            'img' => 'mimes:jpeg,png,gif|max:3102',
        ), array(
            'type.required' => '回复类型缺失',
            'type.in' => '回复类型错误',
            'target.required' => '回复目标错误',
            'content.required' => '回复不能为空',
            'img.mimes' => '图片类型不允许,请上传常规的图片(jpg、png、gif)文件',
            'img.max' => '图片过大,图片大小不得超出3MB',
        ));
        $type = $request->input('type');
        $target = $request->input('target');
        $comment = null;
        if ($type == Comment::TYPE_REPLY) {
            $comment = Comment::where('status', Comment::STATUS_NORMAL)->find($target);
            if (!$comment) {
                return result(20038, '您回复的内容不存在，请刷新页面再试！');
            }
            $topic = $comment->topic;
        } else {
            $topic = Topic::find($target);
        }
        if (!$topic || $topic->status != Topic::STATUS_NORMAL) {
            return result(20037, '您回复的讨论不存在，请刷新页面再试！');
        }
        if ($topic->can_comment == Topic::CAN_COMMENT_FALSE) {
            return result(20039, '您回复的讨论禁止回复！');
        }
        $group = $topic->group;
        if (!$group || $group->status != Group::STATUS_NORMAL) {
            return result(20014, '小组不存在，请刷新页面再试！');
        }
        if (!$group->allMember()->find(session('uid'))) {
            return result(20024, '您不是小组成员，不可以回复该讨论帖！');
        }
        $fullname = "";
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $realPath = $image->getRealPath();
            $hash = md5_file($realPath);
            $savePath = 'uploads/comment';
            is_dir($savePath) || mkdir($savePath);
            // 扩展名
            $ext = $image->getClientOriginalExtension();
            $img_name = $hash . '.' . $ext;
            $fullname = '/' . $savePath . '/' . $img_name;
        }
        $com = new Comment();
        $com->type = $type;
        $com->parent_id = $target;
        $com->u_id = session('uid');
        $com->t_id = $topic->id;
        $com->content = $request->input('content');
        $com->image = $fullname;
        $com->status = Comment::STATUS_NORMAL;
        \DB::beginTransaction();
        try {
            $com->save();
            empty($fullname) or file_exists($savePath . '/' . $img_name) or $image->storeAs($savePath, $img_name, 'addressable');
            $inform = new Inform();
            $inform->setTo($type == Comment::TYPE_REPLY ? $comment->u_id : $topic->u_id)->sendInform($com, $type == Comment::TYPE_REPLY ? 301 : 300, array(
                'u_id' => session('uid'),
                'u_name' => session('uname'),
                't_id' => $topic->id,
                'title' => $topic->title
            ))->save();
            \DB::commit();
            return result(10000, '评论成功！');
        } catch (\Exception $e) {
            \DB::rollBack();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 删除评论
     * @param Request $request
     * @param $cid
     * @return array
     */
    public function del(Request $request, $cid)
    {
        $comment = Comment::where('status', Comment::STATUS_NORMAL)->find($cid);
        if (!$comment) {
            return result(20038, '评论不存在，请刷新后再试！');
        }
        $user = User::find(session('uid'));
        $topic = $comment->topic;
        $group = $topic->group;
        if ($comment->u_id != $user->id && $topic->u_id != $user->id && !$group->leader->find($user->id) && !$group->admin->find($user->id)) {
            return result(20011, '无操作权限！');
        }
        \DB::beginTransaction();
        try {
            $comment->status = Comment::STATUS_DELETED;
            $comment->save();
            if ($comment->u_id != $user->id) {
                $inform = new Inform();
                $inform->setTo($comment->id)->sendInform($topic, 209, array(
                    'content' => $comment->content,
                    't_id' => $topic->id,
                    'title' => $topic->title
                ))->save();
            }
            \DB::commit();
            return result(10000, '删除成功！');
        } catch (\Exception $e) {
            \DB::rollBack();
            dd($e);
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }
}
