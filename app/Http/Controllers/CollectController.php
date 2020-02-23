<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/5/7 0007 0024
 * Time: 21:39
 */

namespace App\Http\Controllers;

use App\Collect;
use App\Topic;
use Illuminate\Http\Request;

class CollectController extends Controller
{
    /**
     * @function 添加收藏
     * @param Request $request
     * @param $target
     * @return array
     */
    public function add(Request $request, $target)
    {
        $topic = Topic::find($target);
        if (!$topic || $topic->status != Topic::STATUS_NORMAL) {
            return result(20037, '您收藏的讨论不存在，请刷新页面再试！');
        }
        $collect = Collect::updateOrCreate(array(
            't_id' => $target,
            'u_id' => session('uid'),
        ), array(
            'status' => Collect::STATUS_NORMAL,
        ));
        return result(10000, '收藏成功！');
    }

    /**
     * @function 取消收藏
     * @param Request $request
     * @param $target
     * @return array
     */
    public function del(Request $request, $target)
    {
        $collect = Collect::where(array(
            'u_id' => session('uid'),
            't_id' => $target,
            'status' => Collect::STATUS_NORMAL
        ))->first();
        if (!$collect) {
            return result(20011, '无操作权限！');
        }
        $collect->status = Collect::STATUS_DELETED;
        $collect->save();
        return result(10000, '取消成功！');
    }
}
