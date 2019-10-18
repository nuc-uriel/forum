<?php


namespace App\Http\Controllers;


use App\Group;
use App\GroupLog;
use App\GroupMember;
use App\Inform;
use App\User;
use Illuminate\Http\Request;
use DB;

class GroupMemberController extends Controller
{
    /**
     * @function 申请加入小组
     * @param Request $request
     * @return array
     */
    public function join(Request $request)
    {
        $gid = $request->input('gid');
        $uid = $request->session()->get('uid');
        $group = Group::find($gid);
        if (!$group) {
            return result(20014, '小组不存在！');
        }
        if ($group->join_way == Group::JOIN_WAY_NOT_ALLOW) {
            return result(20018, '该小组不允许任何人加入！');
        }
        if ($group->blacklist->find($uid)) {
            return result(20019, '您已进入该小组黑名单！');
        }
        if ($group->waitConfirm->find($uid)) {
            return result(20020, '请勿重复申请！');
        }
        DB::beginTransaction();
        try {
            $gm = GroupMember::updateOrCreate(array(
                'u_id' => $uid,
                'g_id' => $gid,
            ), array(
                'role' => GroupMember::ROLE_MEMBER,
                'status' => $group->join_way == Group::JOIN_WAY_ALLOW ? GroupMember::STATUS_NORMAL : GroupMember::STATUS_WAIT_CONFIRM
            ));
            if ($group->join_way == Group::STATUS_WAIT_CONFIRM) {
                $inform = new Inform(array('ut_id' => $group->leader->first()->id));
                $inform->sendInform($gm, 101, array(
                    'u_id' => $uid,
                    'u_name' => session('uname'),
                    'g_id' => $gid,
                    'g_name' => $group->name
                ))->save();
                foreach ($group->admin as $k => $admin) {
                    (new Inform(array('ut_id' => $admin->id)))->sendInform($gm, 101, array(
                        'u_id' => $uid,
                        'u_name' => session('uname'),
                        'g_id' => $gid,
                        'g_name' => $group->name
                    ), $inform->code)->save();
                }
            }
            DB::commit();
            return result(Group::JOIN_WAY_ALLOW ? 10000 : 10001, $group->join_way == Group::JOIN_WAY_ALLOW ? '您已加入该小组！' : '申请已提交，等待管理员审核！');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 退出小组
     * @param Request $request
     * @return array
     */
    public function quit(Request $request)
    {
        $gid = $request->input('gid');
        $uid = $request->session()->get('uid');
        $group = Group::find($gid);
        if (!$group) {
            return result(20014, '小组不存在！');
        }
        if (!$group->allMember->find($uid)) {
            return result(20022, '您不是该小组成员！');
        }
        if ($group->leader->find($uid)) {
            return result(20021, '您是该小组组长，不可退出！');
        }
        $group->allMember()->updateExistingPivot($uid, array(
            'status' => GroupMember::STATUS_DELETED
        ));
        return result(10000, '退出成功！');
    }

    /**
     * @function 转让小组组长
     * @param Request $request
     * @return array
     */
    public function setLeader(Request $request)
    {
        $gid = $request->input('gid');
        $target_id = $request->input('uid');
        $uid = $request->session()->get('uid');
        $group = Group::find($gid);
        if (!$group) {
            return result(20014, '小组不存在！');
        }
        if (!$group->leader->find($uid)) {
            return result(20011, '无操作权限！');
        }
        if (!$group->admin->find($target_id)) {
            return result(20022, '该用户不是管理员！');
        }
        DB::beginTransaction();
        try {
            $my = GroupMember::find($group->allMember->find($uid)->pivot->id);
            $my->role = GroupMember::ROLE_ADMIN;
            $my->timestamps = false;
            $my->save();
            $gm = GroupMember::find($group->allMember->find($target_id)->pivot->id);
            $gm->role = GroupMember::ROLE_LEADER;
            $gm->timestamps = false;
            $gm->save();
            $group->logs()->save((new GroupLog())->addLog(2, 204, array(
                'uid' => $target_id,
                'username' => User::find($target_id)->username,
            )));
            $inform = new Inform();
            $inform->setTo($target_id)->sendInform($gm, 210, array(
                'g_id' => $group->id,
                'g_name' => $group->name,
            ))->save();
            DB::commit();
            return result(10000, '转让成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 任命管理员
     * @param Request $request
     * @return array
     */
    public function appointAdmin(Request $request)
    {
        $gid = $request->input('gid');
        $target_id = $request->input('uid');
        $uid = $request->session()->get('uid');
        $group = Group::find($gid);
        if (!$group) {
            return result(20014, '小组不存在！');
        }
        if (!$group->leader->find($uid)) {
            return result(20011, '无操作权限！');
        }
        if (!$group->allMember->find($target_id)) {
            return result(20022, '该用户不是小组成员！');
        }
        if ($group->admin->count() >= GroupMember::MAX_ADMIN_COUNT) {
            return result(20023, '管理员数量已达到上限！');
        }
        DB::beginTransaction();
        try {
            $gm = GroupMember::find($group->allMember->find($target_id)->pivot->id);
            $gm->role = GroupMember::ROLE_ADMIN;
            $gm->timestamps = false;
            $gm->save();
            $group->logs()->save((new GroupLog())->addLog(2, 201, array(
                'uid' => $target_id,
                'username' => User::find($target_id)->username,
                'opt' => '任命为管理员'
            )));
            $inform = new Inform();
            $inform->setTo($target_id)->sendInform($gm, 203, array(
                'opt' => '任命',
                'role' => '管理员',
                'g_id' => $group->id,
                'g_name' => $group->name,
                'u_id' => session('uid'),
                'u_name' => session('uname')
            ))->save();
            DB::commit();
            return result(10000, '任命成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 撤销管理员
     * @param Request $request
     * @return array
     */
    public function revocationAdmin(Request $request)
    {
        $gid = $request->input('gid');
        $target_id = $request->input('uid');
        $uid = $request->session()->get('uid');
        $group = Group::find($gid);
        if (!$group) {
            return result(20014, '小组不存在！');
        }
        if (!$group->leader->find($uid)) {
            return result(20011, '无操作权限！');
        }
        if (!$group->allMember->find($target_id)) {
            return result(20022, '该用户不是小组成员！');
        }
        if (!$group->admin->find($target_id)) {
            return result(20025, '该用户不是管理员！');
        }
        DB::beginTransaction();
        try {
            $gm = GroupMember::find($group->allMember->find($target_id)->pivot->id);
            $gm->role = GroupMember::ROLE_MEMBER;
            $gm->timestamps = false;
            $gm->save();
            $group->logs()->save((new GroupLog())->addLog(2, 201, array(
                'uid' => $target_id,
                'username' => User::find($target_id)->username,
                'opt' => '降为成员'
            )));
            $inform = new Inform();
            $inform->setTo($target_id)->sendInform($gm, 203, array(
                'opt' => '降',
                'role' => '成员',
                'g_id' => $group->id,
                'g_name' => $group->name,
                'u_id' => session('uid'),
                'u_name' => session('uname')
            ))->save();
            DB::commit();
            return result(10000, '撤销成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @funtion 通过申请
     * @param int $apply_id
     * @param mixed $code
     * @param Request $request
     * @return array
     */
    public function passApply(Request $request, $code = '')
    {
        $target_id = $request->input('uid');
        $gid = $request->input('gid');
        $uid = $request->session()->get('uid');
        $group = Group::find($gid);
        if (!$group) {
            return result(20014, '小组不存在！');
        }
        if (!$group->leader->find($uid) && !$group->admin->find($uid)) {
            return result(20011, '无操作权限！');
        }
        if (!$group->waitConfirm->find($target_id)) {
            return result(20026, '该用户未申请加入小组！');
        }
        DB::beginTransaction();
        try {
            $gm = GroupMember::find($group->waitConfirm->find($target_id)->pivot->id);
            $group->waitConfirm()->updateExistingPivot($target_id, array(
                'status' => GroupMember::STATUS_NORMAL
            ));
            $group->logs()->save((new GroupLog())->addLog(2, 202, array(
                'uid' => $target_id,
                'username' => User::find($target_id)->username,
                'opt' => '通过'
            )));
            if (!$code) {
                $inform = Inform::where(array(
                    'uf_id' => $target_id,
                    'ut_id' => $uid,
                    'type' => 101
                ))->whereIn('status', array(Inform::STATUS_UNREAD, Inform::STATUS_READ))->first();
                $code = $inform->code;
            }
            Inform::where(array(
                'code' => $code,
            ))->update(array(
                'disposer_id' => session('uid'),
                'status' => Inform::STATUS_CONFIRMED
            ));
            $inform = new Inform();
            $inform->setTo($target_id)->sendInform($gm, 202, array(
                'opt' => '同意',
                'g_id' => $group->id,
                'g_name' => $group->name
            ))->save();
            DB::commit();
            return result(10000, '通过成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @funtion 拒绝申请
     * @param Request $request
     * @param mixed $code
     * @return array
     */
    public function refuseApply(Request $request, $code = "")
    {
        $gid = $request->input('gid');
        $target_id = $request->input('uid');
        $uid = $request->session()->get('uid');
        $group = Group::find($gid);
        if (!$group) {
            return result(20014, '小组不存在！');
        }
        if (!$group->leader->find($uid) && !$group->admin->find($uid)) {
            return result(20011, '无操作权限！');
        }
        if (!$group->waitConfirm->find($target_id)) {
            return result(20026, '该用户未申请加入小组！');
        }
        DB::beginTransaction();
        try {
            $gm = GroupMember::find($group->waitConfirm->find($target_id)->pivot->id);
            $group->waitConfirm()->updateExistingPivot($target_id, array(
                'status' => GroupMember::STATUS_NOT_PASS
            ));
            $group->logs()->save((new GroupLog())->addLog(2, 202, array(
                'uid' => $target_id,
                'username' => User::find($target_id)->username,
                'opt' => '拒绝'
            )));
            if (!$code) {
                $inform = Inform::where(array(
                    'uf_id' => $target_id,
                    'ut_id' => $uid,
                    'type' => 101
                ))->whereIn('status', array(Inform::STATUS_UNREAD, Inform::STATUS_READ))->first();
                $code = $inform->code;
            }
            Inform::where(array(
                'code' => $code,
            ))->update(array(
                'disposer_id' => session('uid'),
                'status' => Inform::STATUS_DENIED
            ));
            $inform = new Inform();
            $inform->setTo($target_id)->sendInform($gm, 202, array(
                'opt' => '拒绝',
                'g_id' => $group->id,
                'g_name' => $group->name
            ))->save();
            DB::commit();
            return result(10000, '拒绝成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @funtion 删除成员
     * @param Request $request
     * @return array
     */
    public function delMember(Request $request)
    {
        $gid = $request->input('gid');
        $target_id = $request->input('uid');
        $uid = $request->session()->get('uid');
        $group = Group::find($gid);
        if (!$group) {
            return result(20014, '小组不存在！');
        }
        if (!$group->leader->find($uid) && !$group->admin->find($uid)) {
            return result(20011, '无操作权限！');
        }
        if (!$group->allMember->find($target_id)) {
            return result(20022, '该用户不是小组成员！');
        }
        if ($group->admin->find($target_id) && $group->admin->find($uid)) {
            return result(20011, '无操作权限！');
        }
        DB::beginTransaction();
        try {
            $gm = GroupMember::find($group->allMember->find($target_id)->pivot->id);
            $group->allMember()->updateExistingPivot($target_id, array(
                'status' => GroupMember::STATUS_DELETED
            ));
            $group->logs()->save((new GroupLog())->addLog(2, 203, array(
                'uid' => $target_id,
                'username' => User::find($target_id)->username
            )));
            $inform = new Inform();
            $inform->setTo($target_id)->sendInform($gm, 204, array(
                'g_id' => $group->id,
                'g_name' => $group->name
            ))->save();
            DB::commit();
            return result(10000, '踢出成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 添加黑名单
     * @param Request $request
     * @return array
     */
    public function addBlacklist(Request $request)
    {
        $gid = $request->input('gid');
        $target_id = $request->input('uid');
        $uid = $request->session()->get('uid');
        $group = Group::find($gid);
        if (!$group) {
            return result(20014, '小组不存在！');
        }
        if (!$group->leader->find($uid) && !$group->admin->find($uid)) {
            return result(20011, '无操作权限！');
        }
        if ($group->admin->find($target_id) && $group->admin->find($uid)) {
            return result(20011, '无操作权限！');
        }
        DB::beginTransaction();
        try {
            $group->allMember()->updateExistingPivot($target_id, array(
                'status' => GroupMember::STATUS_BLACKLIST
            ));
            $group->logs()->save((new GroupLog())->addLog(2, 201, array(
                'uid' => $target_id,
                'username' => User::find($target_id)->username,
                'opt' => '加入了黑名单'
            )));
            DB::commit();
            return result(10000, '加入黑名单成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 移出黑名单
     * @param Request $request
     * @return array
     */
    public function delBlacklist(Request $request)
    {
        $gid = $request->input('gid');
        $target_id = $request->input('uid');
        $uid = $request->session()->get('uid');
        $group = Group::find($gid);
        if (!$group) {
            return result(20014, '小组不存在！');
        }
        if (!$group->leader->find($uid) && !$group->admin->find($uid)) {
            return result(20011, '无操作权限！');
        }
        if (!$group->blacklist->find($target_id)) {
            return result(20027, '该用户不在小组黑名单！');
        }
        DB::beginTransaction();
        try {
            $group->blacklist()->updateExistingPivot($target_id, array(
                'status' => GroupMember::STATUS_DELETED
            ));
            $group->logs()->save((new GroupLog())->addLog(2, 201, array(
                'uid' => $target_id,
                'username' => User::find($target_id)->username,
                'opt' => '移出了黑名单'
            )));
            DB::commit();
            return result(10000, '移出黑名单成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }
}