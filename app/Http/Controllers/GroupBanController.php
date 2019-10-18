<?php


namespace App\Http\Controllers;


use App\Group;
use App\GroupBan;
use App\GroupLog;
use Illuminate\Http\Request;
use DB;

class GroupBanController extends Controller
{
    /**
     * @function 添加违禁词
     * @param Request $request
     * @return array
     */
    public function add(Request $request)
    {
        $gid = $request->input('gid');
        $this->validate($request, [
            'word' => "required|max:16|unique:group_ban,word,NULL,id,g_id,{$gid},status," . GroupBan::STATUS_NORMAL,
        ], [
            'required' => ':attribute不能为空',
            'unique' => ':attribute已存在',
            'max' => ':attribute最多:max个字符'
        ], [
            'word' => '违禁词',
        ]);
        $uid = $request->session()->get('uid');
        $group = Group::find($gid);
        if (!$group) {
            return result(20014, '小组不存在！');
        }
        if (!$group->leader->find($uid) && !$group->admin->find($uid)) {
            return result(20011, '无操作权限！');
        }
        DB::beginTransaction();
        try {
            GroupBan::updateOrCreate(array(
                'u_id' => $uid,
                'g_id' => $gid,
            ), array(
                'word' => $request->input('word'),
                'status' => GroupBan::STATUS_NORMAL
            ));
            $log = new GroupLog();
            $log->g_id = $gid;
            $log->addLog(1, 104, array(
                'opt' => '添加',
                'word' => $request->input('word')
            ))->save();
            DB::commit();
            return result(10000, '添加成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 删除违禁词
     * @param Request $request
     * @return array
     */
    public function del(Request $request)
    {
        $gid = $request->input('gid');
        $uid = $request->session()->get('uid');
        $bid = $request->input('bid');
        $group = Group::find($gid);
        if (!$group) {
            return result(20014, '小组不存在！');
        }
        if (!$group->leader->find($uid) && !$group->admin->find($uid)) {
            return result(20011, '无操作权限！');
        }
        if (!$group->banWords->find($bid)) {
            return result(20028, '该条记录不存在！');
        }
        DB::beginTransaction();
        try {
            $log = new GroupLog();
            $log->g_id = $gid;
            $log->addLog(1, 104, array(
                'opt' => '删除',
                'word' => $group->banWords->find($bid)->word
            ))->save();
            $group->banWords->find($bid)->update(array(
                'status' => GroupBan::STATUS_DELETED
            ));
            DB::commit();
            return result(10000, '删除成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }
}