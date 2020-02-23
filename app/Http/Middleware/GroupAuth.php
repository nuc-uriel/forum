<?php

namespace App\Http\Middleware;

use App\Group;
use Closure;

/**
 * function 验证组操作权限
 * Class GroupAuth
 * @package App\Http\Middleware
 */
class GroupAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param  $id
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->has('gid')) {
            $group = Group::find($request->input('gid'));
            $uid = $request->session()->get('uid');
            if ($group && ($group->leader->find($uid) || $group->admin->find($uid))) {
                return $next($request);
            }
        } else {
            if ($request->ajax()) {
                return response()->json(result(20011));
            } else {
                abort(404);
            }
        }
    }
}
