<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * @function 登录验证中间件
 * Class LoginCheck
 * @package App\Http\Middleware
 */
class LoginCheck
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->has('uid')) {
            return $next($request);
        } else {
            if ($request->ajax()) {
                return  response()->json(result(20001));
            } else {
                return redirect()->route('login');
            }
        }
    }
}
