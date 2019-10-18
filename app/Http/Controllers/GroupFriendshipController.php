<?php


namespace App\Http\Controllers;


use App\Group;
use App\GroupFriendship;
use App\Inform;
use DB;
use Illuminate\Http\Request;

class GroupFriendshipController extends Controller
{
    /**
     * @function 设置友情小组
     * @param Request $request
     * @return array
     */
    public function add(Request $request)
    {
        $name = $request->input('name', '');
        $owner_group = Group::find($request->input('gid'));
        $friend_group = Group::whereRaw('name=? AND status=?', array($name, Group::STATUS_NORMAL))->first();
        if (!$owner_group->leader->find(session('uid'))) {
            return result(20011, '无操作权限！');
        }
        if (!$friend_group) {
            return result(20014, '该小组不存在！');
        }
        if ($owner_group->id == $friend_group->id) {
            return result(20015, '不能设置自己为友情小组！');
        }
        if ($owner_group->friendshipAsOwner->count() + $owner_group->friendshipAsFriend->count() >= Group::MAX_FRIEND) {
            return result(20016, '友情小组数量已达到上限！');
        }
        DB::beginTransaction();
        try {
            $friendship = GroupFriendship::where(array(
                'go_id' => $friend_group->id,
                'gf_id' => $owner_group->id
            ))->first() ? GroupFriendship::firstOrNew(array(
                'go_id' => $friend_group->id,
                'gf_id' => $owner_group->id
            )) : GroupFriendship::firstOrNew(array(
                'go_id' => $owner_group->id,
                'gf_id' => $friend_group->id
            ));
            $friendship->status = GroupFriendship::STATUS_WAIT_CONFIRM;
            $friendship->save();
            $inform = new Inform();
            $inform->setFrom($owner_group->leader->first()->id)->setTo($friend_group->leader->first()->id)->sendInform($friendship, 100, array(
                'gf_id' => $owner_group->id,
                'gf_name' => $owner_group->name,
                'gt_id' => $friend_group->id,
                'gt_name' => $friend_group->name,
            ))->save();
            DB::commit();
            return result(10000, '请求已发送，等待对方确认！');
        } catch (\Exception $e) {
            DB::rollback();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 通过友情小组申请
     * @param Request $request
     * @param $code
     * @return array
     */
    public function pass(Request $request, $code = "")
    {
        $friendship = GroupFriendship::find($request->input('fid'));
        if (!$friendship) {
            return result(20030, '链接已失效！');
        }
        if (!$friendship->friend->leader->find(session('uid'))) {
            return result(20011, '无操作权限！');
        }
        if ($friendship->owner->friendshipAsOwner->count() + $friendship->owner->friendshipAsFriend->count() >= Group::MAX_FRIEND) {
            return result(20016, '对方友情小组数量已达到上限！');
        }
        if ($friendship->friend->friendshipAsOwner->count() + $friendship->friend->friendshipAsFriend->count() >= Group::MAX_FRIEND) {
            return result(20016, '友情小组数量已达到上限！');
        }
        DB::beginTransaction();
        try {
            $friendship->update(array(
                'status' => GroupFriendship::STATUS_NORMAL
            ));
            if (!$code) {
                $inform = Inform::where(array(
                    'uf_id' => $friendship->owner->leader->first()->id,
                    'ut_id' => session('uid'),
                    'type' => 100
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
            $inform->setTo($friendship->owner->leader->first()->id)->sendInform($friendship, 200, array(
                'opt' => '同意',
                'gf_id' => $friendship->friend->id,
                'gf_name' => $friendship->friend->name,
                'gt_id' => $friendship->owner->id,
                'gt_name' => $friendship->owner->name,
            ))->save();
            DB::commit();
            return result(10000, '友情小组设置成功！');
        } catch (\Exception $e) {
            DB::rollback();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 拒绝友情小组申请
     * @param Request $request
     * @param $code
     * @return array
     */
    public function refuse(Request $request, $code = "")
    {
        $friendship = GroupFriendship::find($request->input('fid'));
        if (!$friendship) {
            return result(20030, '链接已失效！');
        }
        if (!$friendship->friend->leader->find(session('uid'))) {
            return result(20011, '无操作权限！');
        }
        DB::beginTransaction();
        try {
            $friendship->update(array(
                'status' => GroupFriendship::STATUS_DELETED
            ));
            if (!$code) {
                $inform = Inform::where(array(
                    'uf_id' => $friendship->owner->leader->first()->id,
                    'ut_id' => session('uid'),
                    'type' => 100
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
            $inform->setTo($friendship->owner->leader->first()->id)->sendInform($friendship, 200, array(
                'opt' => '拒绝',
                'gf_id' => $friendship->friend->id,
                'gf_name' => $friendship->friend->name,
                'gt_id' => $friendship->owner->id,
                'gt_name' => $friendship->owner->name,
            ))->save();
            DB::commit();
            return result(10000, '已拒绝！');
        } catch (\Exception $e) {
            DB::rollback();
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 删除友情小组
     * @param Request $request
     * @return array
     */
    public function del(Request $request)
    {
        $gid = $request->input('gid');
        $fid = $request->input('fid');
        $friendship = GroupFriendship::find($fid);
        if ($friendship && ($friendship->go_id == $gid || $friendship->gf_id == $gid)) {
            if (!Group::find($gid)->leader->find(session('uid')) && !Group::find($fid)->leader->find(session('uid'))) {
                return result(20011, '无操作权限！');
            }
            DB::beginTransaction();
            try {
                $friendship->status = GroupFriendship::STATUS_DELETED;
                $friendship->save();
                $inform = new Inform();
                if ($friendship->go_id == $gid) {
                    $inform->setTo($friendship->friend->leader->first()->id)->sendInform($friendship, 201, array(
                        'gf_id' => $friendship->owner->id,
                        'gf_name' => $friendship->owner->name,
                        'gt_id' => $friendship->friend->id,
                        'gt_name' => $friendship->friend->name,
                    ))->save();
                } else {
                    $inform->setTo($friendship->owner->leader->first()->id)->sendInform($friendship, 201, array(
                        'gf_id' => $friendship->friend->id,
                        'gf_name' => $friendship->friend->name,
                        'gt_id' => $friendship->owner->id,
                        'gt_name' => $friendship->owner->name,
                    ))->save();
                }

                return result(10000, '友情小组已取消！');
            } catch (\Exception $e) {
                DB::rollback();
                return result(20000, '网络繁忙，请稍后再试！');
            }
        } else {
            return result(20017, '该小组不是您的友情小组！');
        }
    }
}