<?php


namespace App\Http\Controllers;


use App\Inform;
use App\MemberRelationship;
use App\User;
use DB;
use Illuminate\Http\Request;

class MemberRelationshipController extends Controller
{
    /**
     * @function 添加关注
     * @param Request $request
     * @return array
     */
    public function addIdol(Request $request)
    {
        $target_uid = $request->input('uid');
        $uid = $request->session()->get('uid');
        if($target_uid == $uid){
            return result(20006, '您不可以关注自己！');
        }
        $target = User::find($target_uid);
        if ($target && !$target->blacklist->find($uid)) {
            DB::beginTransaction();
            try{
                $mr = MemberRelationship::updateOrCreate(array(
                    'myself_id' => $uid,
                    'other_id' => $target_uid,
                ), array(
                    'type' => MemberRelationship::TYPE_FANS,
                    'status' => MemberRelationship::STATUS_NORMAL
                ));
                $inform = new Inform();
                $inform->setTo($target_uid)->sendInform($mr, 500, array(
                    'u_id' => $target->id,
                    'u_name' => $target->username
                ))->save();
                DB::commit();
                return result(10000, '关注成功！');
            }catch (\Exception $e){
                DB::rollBack();
                return result(20000, '网络繁忙，请稍后再试！');
            }
        } else {
            return result(20007, '您不可以关注该用户！');
        }
    }

    /**
     * @function 取消关注
     * @param Request $request
     * @return array
     */
    public function delIdol(Request $request)
    {
        $target_uid = $request->input('uid');
        $uid = $request->session()->get('uid');
        if($target_uid == $uid){
            return result(20006, '您不可以关注自己！');
        }
        $target = User::find($target_uid);
        $user = User::find($uid);
        if (!$target) {
            return result(20008, '用户不存在！');
        }
        if (!$user->idol->find($target_uid)) {
            return result(20029, '您没有关注该用户！');
        }
        $user->idol()->updateExistingPivot($target_uid, array(
            'status' => MemberRelationship::STATUS_DELETED
        ));
        return result(10000, '取关成功！');
    }

    /**
     * @function 添加黑名单
     * @param Request $request
     * @return array
     */
    public function addBlackList(Request $request)
    {
        $target_uid = $request->input('uid');
        $uid = $request->session()->get('uid');
        if($target_uid == $uid){
            return result(20009, '您不可以拉黑自己！');
        }
        $target = User::find($target_uid);
        if (!$target) {
            return result(20008, '用户不存在！');
        }
        DB::beginTransaction();
        try {
            if ($target->idol->find($uid)) {
                $target->idol()->updateExistingPivot($uid, array(
                    'status' => MemberRelationship::STATUS_DELETED
                ));
            }
            MemberRelationship::updateOrCreate(array(
                'myself_id' => $uid,
                'other_id' => $target_uid
            ), array(
                'type' => MemberRelationship::TYPE_BLACKLIST,
                'status' => MemberRelationship::STATUS_NORMAL
            ));
            DB::commit();
            return result(10000, '拉黑成功！');
        } catch (\Exception $e) {
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @funtion 取消黑名单
     * @param Request $request
     * @return array
     */
    public function delBlackList(Request $request)
    {
        $target_uid = $request->input('uid');
        $uid = $request->session()->get('uid');
        if($target_uid == $uid){
            return result(20009, '您不可以拉黑自己！');
        }
        $target = User::find($target_uid);
        $user = User::find($uid);
        if (!$target) {
            return result(20008, '用户不存在！');
        }
        if (!$user->blacklist->find($target_uid)) {
            return result(20010, '该用户不在您黑名单中！');
        }
        $user->blacklist()->updateExistingPivot($target_uid, array(
            'status' => MemberRelationship::STATUS_DELETED
        ));
        return result(10000, '取消拉黑成功！');
    }
}