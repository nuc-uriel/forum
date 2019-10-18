<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/5/8 0008 0024
 * Time: 22:55
 */

namespace App\Http\Controllers;


use App\Group;
use App\GroupType;
use App\Topic;
use App\User;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * @function 展示首页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = [
            'type' => array(
                'name' => '讨论精选',
                'introduce' => '',
            )
        ];
        $topic_where = array(
            ['topic.status', Topic::STATUS_NORMAL],
            ['topic.created_at', '>=', strtotime('-1 month')],
            ['group.status', Group::STATUS_NORMAL],
        );
        $group_where = array(
            ['group.status', Group::STATUS_NORMAL],
        );
        $type = $request->input('type', -1);
        if ($type != -1) {
            $type_model = GroupType::find($type);
            if ($type_model) {
                $topic_where['group.gt_id'] = $type;
                $topic_where['group.gt_id'] = $type;
                $data['type'] = array(
                    'name' => $type_model->name,
                    'introduce' => $type_model->introduce
                );
            }
        }
        $topics = Topic::where($topic_where)->has('comments')->withCount('comments')->leftJoin('group', 'topic.g_id', 'group.id')->orderBy('comments_count', 'desc')->paginate(1);
        $groups = Group::where($group_where)->withCount('topics')->orderBy('topics_count', 'desc')->take(10)->get();
        $data['topics'] = $topics;
        $data['groups'] = $groups;
        if (session('uid')) {
            $data['user'] = User::find(session('uid'));
        }
        return view('front.index.index', $data);
    }

    /**
     * @function 获取搜索结果
     * @param Request $request
     * @param $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request, $type='topic')
    {
        in_array($type, array('topic', 'group', 'member')) or $type = 'topic';
        $keyword = $request->input('keyword');
        $order = $request->input('order');
        switch ($type) {
            case 'group':
                $res = Group::search(array(
                    'query_type' => 'multi_match',
                    'query'=>$keyword,
                    'fields' => array(
                        'name^2',
                        'labels'
                    ),
                ))->where('status', Group::STATUS_NORMAL)->paginate(5);
                break;
            case 'member':
                $res = User::search(array(
                    'query_type' => 'multi_match',
                    'query'=>$keyword,
                    'fields' => array(
                        'username^2',
                        'signature',
                        'introduce'
                    ),
                ))->where('status',  User::STATUS_NORMAL)->paginate(5);
                break;
            default:
                if ($order == 'new'){
                    $res = Topic::search(array(
                        'query_type' => 'multi_match',
                        'query'=>$keyword,
                        'fields' => array(
                            'title^2',
                            'content'
                        ),
                    ))->where('status', Topic::STATUS_NORMAL)->orderBy('created_at', 'desc')->paginate(5);
                }else{
                    $res = Topic::search(array(
                        'query_type' => 'multi_match',
                        'query'=>$keyword,
                        'fields' => array(
                            'title^2',
                            'content'
                        ),
                    ))->where('status', Topic::STATUS_NORMAL)->paginate(5);
                }
        }
        return view('front.index.search', array(
            'type' => $type,
            'data' => $res
        ));
    }
}