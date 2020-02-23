<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/4/24 0024
 * Time: 15:42
 */

namespace App\Http\Controllers;

use App\Group;
use App\GroupLabel;
use App\GroupLog;
use App\GroupMember;
use App\GroupType;
use App\Topic;
use Illuminate\Http\Request;
use DB;
use Storage;

class GroupController extends Controller
{
    private $pageTabs = array(
        'base-info',
        'other-set',
        'data-statistics',
        'member-edit',
        'ban-manage',
        'log',
    );

    /**
     * @function 添加组
     * @param Request $request
     * @return array|\Illuminate\Http\Response
     */
    public function build(Request $request)
    {
        if ($request->isMethod('GET')) {
            $group_type = GroupType::all();
            return response()->view('front.group.build', array('group_type' => $group_type));
        } else {
            $this->validate($request, [
                'name' => 'required|between:3,10|unique:group,name',
                'introduce' => 'required|string|between:3,5000',
                'group_type' => 'required|exists:group_type,id,status,0',
                'path' => 'required|file_exists:public_path'
            ], [
                'required' => ':attribute不能为空',
                'unique' => ':attribute已被使用',
                'string' => ':attribute需为字符串',
                'between' => ':attribute最小长度为:min，最大长度为:max',
                'exists' => ':attribute不存在',
                'file_exists' => ':attribute错误'
            ], [
                'name' => '小组名称',
                'introduce' => '小组介绍',
                'group_type' => '小组类别',
                'path' => '小组图标'
            ]);
            $group = new Group();
            $group->u_id = $request->session()->get('uid');
            $group->name = $request->input('name');
            $group->introduce = $request->input('introduce');
            $group->gt_id = $request->input('group_type');
            $group->icon = '/uploads/icon/' . uniqid() . '.png';
            $group->status = Group::STATUS_WAIT_CONFIRM;
            $gl = !empty($request->input('group_label', '')) ? explode(' ', $request->input('group_label')) : array();
            $gl = array_map(function ($l) {
                return mb_substr($l, 0, GroupLabel::MAX_LENGTH);
            }, $gl);
            $gl = array_slice(array_unique($gl), 0, 5);
            $glm = array();
            foreach ($gl as $k => $v) {
                $glm[] = new GroupLabel(['name' => $v, 'status' => GroupLabel::STATUS_WAIT_CONFIRM]);
            }
            DB::beginTransaction();
            try {
                $group->save();
                $group->labels()->saveMany($glm);
                $gm = new GroupMember();
                $gm->u_id = $group->u_id;
                $gm->g_id = $group->id;
                $gm->role = GroupMember::ROLE_LEADER;
                $gm->status = GroupMember::STATUS_NORMAL;
                $gm->save();
                Storage::disk('addressable')->move($request->input('path'), $group->icon);
                DB::commit();
                return result(10000, '申请已提交，请等待管理员审核');
            } catch (\Exception $e) {
                DB::rollback();
                return result(20000, $e->getMessage());
            }
        }
    }

    /**
     * @function 更新组
     * @param Request $request
     * @param string $opt
     * @return array|\Illuminate\Http\Response
     */
    public function edit(Request $request, $opt = 'base-info')
    {
        $opt = in_array($opt, $this->pageTabs) ? $opt : 'base-info';
        $group = Group::where('status', Group::STATUS_NORMAL)->find($request->input('gid'));
        if ($request->isMethod('GET')) {
            return response()->view('front.group.edit', array('group' => $group, 'opt' => $opt));
        } else {
            $this->validate($request, [
                'name' => "required|between:3,10|unique:group,name,{$group->id}",
                'introduce' => 'required|string|between:3,5000',
                'path' => 'required|file_exists:public_path',
                'admin_as' => 'string|max:5',
                'member_as' => 'string|max:5',
            ], [
                'required' => ':attribute不能为空',
                'unique' => ':attribute已被使用',
                'string' => ':attribute需为字符串',
                'between' => ':attribute最小长度为:min，最大长度为:max',
                'file_exists' => ':attribute错误',
                'max' => ':attribute最多:max个字符'
            ], [
                'name' => '小组名称',
                'introduce' => '小组介绍',
                'path' => '小组图标',
                'admin_as' => '管理员名称',
                'member_as' => '成员名称',
            ]);
            $logs = array();
            if ($group->name != $request->input('name')) {
                $logs[] = (new GroupLog())->addLog(1, 101, array(
                    'setting' => '小组名称',
                    'data' => $request->input('name')
                ));
            }
            $group->name = $request->input('name');
            if ($group->introduce != $request->input('introduce')) {
                $logs[] = (new GroupLog())->addLog(1, 101, array(
                    'setting' => '小组介绍',
                    'data' => $request->input('introduce')
                ));
            }
            $group->introduce = $request->input('introduce');
            if ($group->admin_as != $request->input('admin_as')) {
                $logs[] = (new GroupLog())->addLog(1, 101, array(
                    'setting' => '管理员名称',
                    'data' => $request->input('admin_as')
                ));
            }
            $group->admin_as = $request->input('admin_as');
            if ($group->member_as != $request->input('member_as')) {
                $logs[] = (new GroupLog())->addLog(1, 101, array(
                    'setting' => '成员名称',
                    'data' => $request->input('member_as')
                ));
            }
            $group->member_as = $request->input('member_as');
            if ($request->input('path') != $group->icon) {
                $logs[] = (new GroupLog())->addLog(1, 102);
            }
            $gl = !empty($request->input('group_label', '')) ? explode(' ', $request->input('group_label')) : array();
            $gl = array_map(function ($l) {
                return mb_substr($l, 0, GroupLabel::MAX_LENGTH);
            }, $gl);
            $gl = array_slice(array_unique($gl), 0, 5);
            $old_gl = $group->labels->pluck('name')->toArray();
            $add_gl = array_diff($gl, $old_gl);
            $del_gl = array_diff($old_gl, $gl);
            $glm = array();
            foreach ($add_gl as $k => $v) {
                $glm[] = new GroupLabel(['name' => $v, 'status' => GroupLabel::STATUS_NORMAL]);
                $logs[] = (new GroupLog())->addLog(1, 103, array(
                    'opt' => '添加',
                    'label' => $v
                ));
            }
            foreach ($del_gl as $k => $v) {
                $logs[] = (new GroupLog())->addLog(1, 103, array(
                    'opt' => '删除',
                    'label' => $v
                ));
            }
            DB::beginTransaction();
            try {
                $group->save();
                $group->labels()->saveMany($glm);
                if ($del_gl) {
                    $group->labels()->whereIn('name', $del_gl)->update(array('status' => GroupLabel::STATUS_DELETED));
                }
                if ($request->input('path') != $group->icon) {
                    Storage::disk('addressable')->delete($group->icon);
                    Storage::disk('addressable')->move($request->input('path'), $group->icon);
                }
                $group->logs()->saveMany($logs);
                DB::commit();
                return result(10000, '更新成功！');
            } catch (\Exception $e) {
                DB::rollback();
                return result(20000, '网络繁忙，请稍后再试！');
            }
        }
    }

    /**
     * @function 保存用户上传图标原文件并返回裁剪
     * @param Request $request
     * @return array
     */
    public function setIcon(Request $request)
    {
        $allow_mime_type = array(
            'image/gif',
            'image/png',
            'image/jpeg'
        );
        $icon = $request->file('icon');
        if ($icon->isValid() && in_array($icon->getMimeType(), $allow_mime_type)) {
            $path = $request->file('icon')->store('/temp/icon', 'addressable');
            return result(10000, '/' . $path);
        } else {
            return result(20012, '文件错误！');
        }
    }

    /**
     * @function 保存用户裁剪后的图标
     * @param Request $request
     * @return array
     */
    public function saveIcon(Request $request)
    {
        $image_p = imagecreatetruecolor(132, 132);
        $image = imagecreatefromjpeg(public_path($request->get('path')));
        imagecopyresampled($image_p, $image, 0, 0, $request->get('x'), $request->get('y'), 132, 132, $request->get('width'), $request->get('height'));
        $path = '/temp/icon_processed/' . uniqid() . '.png';
        $full_path = public_path($path);
        imagejpeg($image_p, $full_path, 100);
        return result(10000, $path);
    }

    /**
     * @function 设置加入组的方式
     * @param Request $request
     * @return array
     */
    public function setJoinWay(Request $request)
    {
        $group = Group::whereRaw('u_id=? AND status=?', array($request->session()->get('uid'), Group::STATUS_NORMAL))->find($request->input('gid'));
        if ($request->has('join_way') && in_array($request->input('join_way'), array(Group::JOIN_WAY_ALLOW, Group::JOIN_WAY_APPLY, Group::JOIN_WAY_NOT_ALLOW))) {
            $group->join_way = $request->input('join_way');
            DB::beginTransaction();
            try {
                $group->save();
                $group->logs()->save((new GroupLog())->addLog(1, 101, array(
                    'setting' => '成员加入方式',
                    'data' => $request->input('join_way') == Group::JOIN_WAY_ALLOW ? '任何人都能加入' : ($request->input('join_way') == Group::JOIN_WAY_APPLY ? '需要申请，组长/管理员批准后才能加入' : '不允许任何人加入')
                )));
                DB::commit();
                return result(10000, '更新成功！');
            } catch (\Exception $e) {
                DB::rollBack();
                return result(20000, '网络繁忙，请稍后再试！');
            }
        }
        return result(20013, '模式错误，请刷新页面！');
    }

    /**
     * @function 小组搜索联想词
     * @param Request $request
     * @return array
     */
    public function getAssociationalGroups(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $groups = Group::search(array(
            'query_type' => 'must',
            'name' => $keyword,
        ))->where('status', Group::STATUS_NORMAL)->take(5)->get();
        $group_names = array_map(function ($group) {
            return $group['name'];
        }, $groups->toArray());
        return result(10000, $group_names);
    }

    /**
     * @function 展示小组主题
     * @param Request $request
     * @param $gid
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $gid)
    {
        $group = Group::find($gid);
        if ($group) {
            $order = $request->input('order', 'hot');
            if ($order == 'new') {
                $topics = $group->topics()->withCount('comments')->orderBy('is_top', 'desc')->orderBy('created_at', 'desc')->take(20)->get();
            } else {
                $topics = $group->topics()->withCount('comments')->orderBy('is_top', 'desc')->orderBy('comments_count', 'desc')->take(20)->get();
            }

            return response()->view('front.group.show', array(
                'group' => $group,
                'topics' => $topics,
            ));
        } else {
            abort(404);
        }
    }

    /**
     * @function 展示小组讨论
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function topics(Request $request)
    {
        $group = Group::find($request->input('gid'));
        if ($group) {
            $topics = $group->topics()->withCount('comments')->orderBy('is_top', 'desc')->orderBy('created_at', 'desc')->paginate(20);
            return response()->view('front.group.topics', array(
                'group' => $group,
                'topics' => $topics,
            ));
        } else {
            abort(404);
        }
    }

    /**
     * @function 展示小组成员
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function members(Request $request)
    {
        $group = Group::find($request->input('gid'));
        if ($group) {
            return response()->view('front.group.members', array(
                'group' => $group,
            ));
        } else {
            abort(404);
        }
    }

    /**
     * @function 组内搜索讨论
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $group = Group::find($request->input('gid'));
        if ($group) {
            $keyword = $request->input('keyword');
            $order = $request->input('order');
            if ($order == 'new') {
                $topics = Topic::search(array(
                    'query_type' => 'multi_match',
                    'query'=>$keyword,
                    'fields' => array(
                        'title^2',
                        'content'
                    ),
                ))->where('g_id', $group->id)->where('status', Topic::STATUS_NORMAL)->orderBy('created_at', 'desc')->paginate(20);
            } else {
                $topics = Topic::search(array(
                    'query_type' => 'multi_match',
                    'query'=>$keyword,
                    'fields' => array(
                        'title^2',
                        'content'
                    ),
                ))->where('g_id', $group->id)->where('status', Topic::STATUS_NORMAL)->paginate(20);
            }
            return response()->view('front.group.search', array(
                'group' => $group,
                'topics' => $topics,
            ));
        } else {
            abort(404);
        }
    }

    /**
     * @function 获取全部统计数据
     * @param Request $request
     * @return array
     */
    public function getAllChart(Request $request)
    {
        $range = $request->input('range', 'week');
        in_array($range, array('month', 'week')) or $range='week';
        $gid = $request->input('gid');
        $group = Group::find($gid);
        $start_time = strtotime(date('Y-m-d', strtotime("-1 " . $range)))+86400;
        $res = [];
        $res['topic'] = $group->topics()->where('created_at', '>=', $start_time)->count();
        $res['comment'] = $group->comments()->whereRaw('`comment`.`created_at`>=?', [$start_time])->count();
        $res['in'] = $group->allMember()->wherePivot('updated_at', '>=', $start_time)->count();
        $res['out'] = $group->outMember()->wherePivot('updated_at', '>=', $start_time)->count();
        return $res;
    }

    /**
     * @function 获取详细统计数据
     * @param Request $request
     * @return Group[]|\App\Group[][]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Collection[]|\Illuminate\Database\Eloquent\Model[]|\Illuminate\Support\Collection|mixed|null[]
     */
    public function getChart(Request $request)
    {
        $chart = $request->input('chart', 'topic');
        in_array($chart, array('topic', 'comment', 'in', 'out')) or $chart='topic';
        $range = $request->input('range', 'week');
        in_array($range, array('month', 'week')) or $range='week';
        $gid = $request->input('gid');
        $group = Group::find($gid);
        $start_time = strtotime(date('Y-m-d', strtotime("-1 " . $range)))+86400;
        switch ($chart) {
            case 'topic':
                $res = $group->topics()->where('created_at', '>=', $start_time)->get()->groupBy(function ($item, $key) {
                    return strtotime($item->created_at->format('Y-m-d'));
                })->map(function ($item, $key) {
                    return $item->count();
                });
                break;
            case 'comment':
                $res = $group->comments()->whereRaw('`comment`.`created_at`>=?', [$start_time])->get()->groupBy(function ($item, $key) {
                    return strtotime($item->created_at->format('Y-m-d'));
                })->map(function ($item, $key) {
                    return $item->count();
                });
                break;
            case 'in':
                $res = $group->allMember()->wherePivot('updated_at', '>=', $start_time)->get()->groupBy(function ($item, $key) {
                    return strtotime($item->pivot->updated_at->format('Y-m-d'));
                })->map(function ($item, $key) {
                    return $item->count();
                });
                break;
            case 'out':
                $res = $group->outMember()->wherePivot('updated_at', '>=', $start_time)->get()->groupBy(function ($item, $key) {
                    return strtotime($item->pivot->updated_at->format('Y-m-d'));
                })->map(function ($item, $key) {
                    return $item->count();
                });
                break;
        }
        $end_time = time();
        for (; $start_time<$end_time; $start_time+=86400) {
            $res->has($start_time) or $res[$start_time] = 0;
        }
        return $res;
    }
}
