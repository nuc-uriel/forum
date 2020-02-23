<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/4/24 0024
 * Time: 11:28
 */

namespace App\Http\Controllers;

use App\Group;
use App\GroupLog;
use App\Inform;
use App\Topic;
use App\User;
use Illuminate\Http\Request;
use Storage;
use DB;

class TopicController extends Controller
{
    /**
     * @funtion 添加主题
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        $gid = $request->input('gid');
        $group = Group::find($gid);
        if (!$group) {
            return view('front.tips', array(
                'tips' => '小组不存在',
                'message' => '到<a href="/index">主页</a>去看看吧！'
            ));
        }
        if (!$group->allMember()->find(session('uid'))) {
            return view('front.tips', array(
                'tips' => '您不是小组成员，不能发言',
                'message' => '快去<a href="/group/' . $group->id . '">加入</a>他们吧！'
            ));
        }
        if ($request->isMethod('get')) {
            return view('front.topic.edit', array(
                'group' => $group,
                'user' => User::find(session('uid'))
            ));
        } else {
            if (!$request->input('title')) {
                return result(20034, '标题不能为空！');
            }
            if (!trim(strip_tags($request->input('content'), '<img>'))) {
                return result(20035, '讨论不能为空！');
            }
            $words = $group->banWords->pluck('word');
            $title = $request->input('title');
            $content = $request->input('content');
            $str_content = strip_tags($content);
            $ban_word = '';
            foreach ($words as $word) {
                if (mb_stristr($title, $word)) {
                    $ban_word = $word;
                    break;
                }
                if (mb_stristr($str_content, $word)) {
                    $ban_word = $word;
                    break;
                }
            }
            if ($ban_word) {
                return result(20036, '讨论中包含违禁词：' . $ban_word . '，请修改后再发表');
            }
            $path = '/uploads/topic';
            is_dir(public_path($path)) or mkdir(public_path($path));
            $content = preg_replace_callback('/(<img.*?src=["|\'])(.+?)(["|\'].*?>)/', function ($matches) use ($path) {
                $file_name = mb_strrchr($matches[2], '/', false);
                file_exists(public_path($path . $file_name)) or Storage::disk('addressable')->copy($matches[2], $path . $file_name);
                return $matches[1] . $path . $file_name . $matches[3];
            }, $content);
            $topic = new Topic();
            $topic->u_id = session('uid');
            $topic->g_id = $gid;
            $topic->title = $title;
            $topic->content = $content;
            $topic->is_top = Topic::IS_TOP_FALSE;
            $topic->can_comment = Topic::CAN_COMMENT_TRUE;
            $topic->status = Topic::STATUS_NORMAL;
            $topic->save();
            return array_merge(result(10000, '发表成功！'), ['tid'=>$topic->id]);
        }
    }

    /**
     * @funtion 编辑主题
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        $tid = $request->input('tid');
        $topic = Topic::where('status', "!=", Topic::STATUS_DELETED)->find($tid);
        if (!$topic) {
            return view('front.tips', array(
                'tips' => '主题不存在',
                'message' => '到<a href="/index">主页</a>去看看吧！'
            ));
        }
        $group = $topic->group;
        if (!$group->allMember()->find(session('uid'))) {
            return view('front.tips', array(
                'tips' => '您不是小组成员，不能发言',
                'message' => '快去<a href="/group/' . $group->id . '">加入</a>他们吧！'
            ));
        }
        if ($topic->creator->id != session('uid')) {
            return view('front.tips', array(
                'tips' => '您不是发贴人，不能编辑',
                'message' => '到<a href="/index">主页</a>去看看吧！'
            ));
        }
        if ($request->isMethod('get')) {
            return view('front.topic.edit', array(
                'group' => $topic->group,
                'topic' => $topic,
                'user' => User::find(session('uid'))
            ));
        } else {
            if (!$request->input('title')) {
                return result(20034, '标题不能为空！');
            }
            if (!trim(strip_tags($request->input('content'), '<img>'))) {
                return result(20035, '讨论不能为空！');
            }
            $words = $group->banWords->pluck('word');
            $title = $request->input('title');
            $content = $request->input('content');
            $str_content = strip_tags($content);
            $ban_word = '';
            foreach ($words as $word) {
                if (mb_stristr($title, $word)) {
                    $ban_word = $word;
                    break;
                }
                if (mb_stristr($str_content, $word)) {
                    $ban_word = $word;
                    break;
                }
            }
            if ($ban_word) {
                return result(20036, '讨论中包含违禁词：' . $ban_word . '，请修改后再发表');
            }
            $path = '/uploads/topic';
            is_dir(public_path($path)) or mkdir(public_path($path));
            $old_content = $topic->content;
            preg_match_all('/<img.*?src=["|\'](.+?)["|\'].*?>/', $old_content, $images, PREG_SET_ORDER);
            $old_imgs = [];
            foreach ($images as $image) {
                $old_imgs[] = mb_strrchr($image[1], '/', false);
            }
            $imgs = [];
            $content = preg_replace_callback('/(<img.*?src=["|\'])(.+?)(["|\'].*?>)/', function ($matches) use ($path) {
                $file_name = mb_strrchr($matches[2], '/', false);
                $imgs[] = $file_name;
                file_exists(public_path($path . $file_name)) or Storage::disk('addressable')->copy($matches[2], $path . $file_name);
                return $matches[1] . $path . $file_name . $matches[3];
            }, $content);
            $del_imgs = array_diff($old_imgs, $imgs);
            $topic->title = $title;
            $topic->content = $content;
            $topic->save();
            $del_imgs = array_map(function ($value) use ($path) {
                return $path . $value;
            }, $del_imgs);
            Storage::disk('addressable')->delete($del_imgs);
            return array_merge(result(10000, '修改成功！'), ['tid'=>$topic->id]);
        }
    }

    /**
     * @function 删除讨论帖
     * @param Request $request
     * @return array
     */
    public function del(Request $request)
    {
        $tid = $request->input('tid');
        $topic = Topic::where('status', "!=", Topic::STATUS_DELETED)->find($tid);
        if (!$topic) {
            return result(20037, '讨论不存在！');
        }
        if ($topic->creator->id == session('uid')) {
            $topic->status = Topic::STATUS_DELETED;
            $topic->save();
            return result(10000, '删除成功！');
        } elseif ($topic->group->leader->find(session('uid')) or $topic->group->admin->find(session('uid'))) {
            DB::beginTransaction();
            try {
                $topic->status = Topic::STATUS_DELETED;
                $topic->save();
                $group = $topic->group;
                $target = $topic->creator;
                $group->logs()->save((new GroupLog())->addLog(3, 304, array(
                    'tid' => $topic->id,
                    'title' => $topic->title,
                    'uid' => $target->id,
                    'username' => $target->username
                )));
                $inform = new Inform();
                $inform->setTo($target->id)->sendInform($topic, 208, array(
                    'g_id' => $group->id,
                    'g_name' => $group->name,
                    't_id' => $topic->id,
                    'title' => $topic->title
                ))->save();
                DB::commit();
                return result(10000, '删除成功！');
            } catch (\Exception $e) {
                DB::rollBack();
                return result(20000, '网络繁忙，请稍后再试！');
            }
        } else {
            return result(20011, '无操作权限！');
        }
    }

    /**
     * @function 置顶讨论帖
     * @param mixed $is_top
     * @param Request $request
     * @return array
     */
    public function isTop(Request $request, $is_top)
    {
        $tid = $request->input('tid');
        $topic = Topic::where('status', Topic::STATUS_NORMAL)->find($tid);
        if (!$topic) {
            return result(20037, '讨论不存在！');
        }
        if (!in_array($is_top, array(Topic::IS_TOP_TRUE, Topic::IS_TOP_FALSE))) {
            return result(20000, '网络繁忙，请稍后再试！');
        }
        if ($topic->group->leader->find(session('uid')) or $topic->group->admin->find(session('uid'))) {
            DB::beginTransaction();
            try {
                $topic->is_top = $is_top;
                $topic->save();
                $group = $topic->group;
                $target = $topic->creator;
                $group->logs()->save((new GroupLog())->addLog(3, 301, array(
                    'opt' => $is_top == Topic::IS_TOP_FALSE ? '取消了' : '',
                    'tid' => $topic->id,
                    'title' => $topic->title,
                    'uid' => $target->id,
                    'username' => $target->username
                )));
                $inform = new Inform();
                $inform->setTo($target->id)->sendInform($topic, 205, array(
                    'opt' => $is_top == Topic::IS_TOP_FALSE ? '取消了' : '',
                    'g_id' => $group->id,
                    'g_name' => $group->name,
                    't_id' => $topic->id,
                    'title' => $topic->title
                ))->save();
                DB::commit();
                return result(10000, '设置成功！');
            } catch (\Exception $e) {
                DB::rollBack();
                return result(20000, '网络繁忙，请稍后再试！');
            }
        } else {
            return result(20011, '无操作权限！');
        }
    }

    /**
     * @function 设置评论
     * @param mixed $can_comment
     * @param Request $request
     * @return array
     */
    public function canComment(Request $request, $can_comment)
    {
        $tid = $request->input('tid');
        $topic = Topic::where('status', Topic::STATUS_NORMAL)->find($tid);
        if (!$topic) {
            return result(20037, '讨论不存在！');
        }
        if (!in_array($can_comment, array(Topic::CAN_COMMENT_TRUE, Topic::CAN_COMMENT_FALSE))) {
            return result(20000, '网络繁忙，请稍后再试！');
        }
        if ($topic->group->leader->find(session('uid')) or $topic->group->admin->find(session('uid'))) {
            DB::beginTransaction();
            try {
                $topic->can_comment = $can_comment;
                $topic->save();
                $group = $topic->group;
                $target = $topic->creator;
                $group->logs()->save((new GroupLog())->addLog(3, 302, array(
                    'opt' => $can_comment == Topic::CAN_COMMENT_TRUE ? '允许' : '禁止',
                    'tid' => $topic->id,
                    'title' => $topic->title,
                    'uid' => $target->id,
                    'username' => $target->username
                )));
                $inform = new Inform();
                $inform->setTo($target->id)->sendInform($topic, 206, array(
                    'opt' => $can_comment == Topic::CAN_COMMENT_TRUE ? '允许' : '禁止',
                    'g_id' => $group->id,
                    'g_name' => $group->name,
                    't_id' => $topic->id,
                    'title' => $topic->title
                ))->save();
                DB::commit();
                return result(10000, '设置成功！');
            } catch (\Exception $e) {
                DB::rollBack();
                return result(20000, '网络繁忙，请稍后再试！');
            }
        } else {
            return result(20011, '无操作权限！');
        }
    }

    /**
     * @function 封禁主题
     * @param Request $request
     * @param $ban
     * @return array
     */
    public function ban(Request $request, $ban)
    {
        $tid = $request->input('tid');
        $topic = Topic::where('status', '!=', Topic::STATUS_DELETED)->find($tid);
        if (!$topic) {
            return result(20037, '讨论不存在！');
        }
        if (!in_array($ban, array(Topic::STATUS_NORMAL, Topic::STATUS_BAN))) {
            return result(20000, '网络繁忙，请稍后再试！');
        }
        if ($topic->group->leader->find(session('uid')) or $topic->group->admin->find(session('uid'))) {
            DB::beginTransaction();
            try {
                $topic->status = $ban;
                $topic->save();
                $group = $topic->group;
                $target = $topic->creator;
                $group->logs()->save((new GroupLog())->addLog(3, 303, array(
                    'opt' => $ban == Topic::STATUS_BAN ? '入' : '出',
                    'tid' => $topic->id,
                    'title' => $topic->title,
                    'uid' => $target->id,
                    'username' => $target->username
                )));
                $inform = new Inform();
                $inform->setTo($target->id)->sendInform($topic, 207, array(
                    'opt' => $ban == Topic::STATUS_BAN ? '入' : '出',
                    'g_id' => $group->id,
                    'g_name' => $group->name,
                    't_id' => $topic->id,
                    'title' => $topic->title
                ))->save();
                DB::commit();
                return result(10000, '设置成功！');
            } catch (\Exception $e) {
                DB::rollBack();
                return result(20000, '网络繁忙，请稍后再试！');
            }
        } else {
            return result(20011, '无操作权限！');
        }
    }

    /**
     * @function 展示主题
     * @param mixed $tid
     * @param Request $request
     * @param $tid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, $tid)
    {
        $topic = Topic::where('status', '!=', Topic::STATUS_DELETED)->find($tid);
        if (!$topic || ($topic->status == Topic::STATUS_BAN && ($topic->creator->id != session('uid') && !$topic->group->leader->find(session('uid')) && !$topic->group->admin->find(session('uid'))))) {
            return view('front.tips', array(
                'tips' => '主题不存在',
                'message' => '到<a href="/index">主页</a>去看看吧！'
            ));
        } else {
            return view('front.topic.show', array(
                'topic' => $topic,
                'page'=>1
            ));
        }
    }
}
