<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/4/17 0017
 * Time: 18:48
 */

namespace App\Http\Controllers;

use App\Jobs\SendEmail;
use Illuminate\Http\Request;
use App\User;
use DB;

class UserController extends Controller
{
    private $pageTabs = array(
        'groups',
        'issue',
        'respond',
        'collect',
        'idol',
        'fans',
        'blacklist',
        'setting',
    );

    /**
     * @function 检查用户名是否已存在
     * @param Request $request
     * @return array
     */
    public function checkName(Request $request)
    {
        if ($request->has('username')) {
            if (DB::table('user')->where('username', $request->get('username'))->first()) {
                return result(20002, '用户名已存在！');
            } else {
                return result(10000, '用戶名可用');
            }
        }
    }

    /**
     * @function 注册
     * @param Request $request
     * @return array
     */
    public function register(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('front.member.register');
        }
        $this->validate($request, [
            'captcha' => 'required|captcha',
            'username' => 'required|between:3,10|unique:user,username',
            'password' => 'required|between:8,16',
            'cpwd' => 'required|same:password',
            'email' => 'required|email|unique:user,email'
        ], [
            'required' => ':attribute不能为空',
            'cpwd.required' => '请再次输入密码',
            'username.unique' => '用户名已存在',
            'email.unique' => '邮箱已被使用',
            'between' => ':attribute最小长度为:min，最大长度为:max',
            'same' => '两次密码输入不一致',
            'captcha' => '验证码错误',
            'email' => ':attribute 格式错误'
        ], [
            'username' => '用户名',
            'password' => '密码',
            'captcha' => '验证码',
            'email' => '邮箱'
        ]);
        $user = new User();
        $user->username = $request->input('username');
        $user->password = sha1($request->input('password'));
        $user->email = $request->input('email');
        $user->confirmation = str_random(16);
        $user->code = uniqid(str_random(3));
        if ($user->save()) {
            $parameters = [
                'username' => $user->username,
                'confirmation' => $user->confirmation,
                'domain' => env('MAIL_APP_URL')
            ];
            $job = (new SendEmail('email.register', $parameters, $user->email, '扬帆小组-账户激活'))->onQueue('email');
            dispatch($job);
            return result(10000, '注册成功！<br/ >请前往邮箱激活账号');
        } else {
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 通过邮件激活账号
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function activate(Request $request)
    {
        $tips = '注册激活链接无效';
        $message = '请不要重复激活，也许你已经注册成功，请 <a href="/login">登录</a> 试试。';
        if ($request->has('confirmation')) {
            $user = User::whereRaw("confirmation=?", array($request->get('confirmation')))->first();
            if ($user) {
                $user->confirmation = '';
                $user->status = User::STATUS_NORMAL;
                if ($user->save()) {
                    $tips = '账号激活成功';
                    $message = '您的账号已经激活啦，现在可以去 <a href="/login">登录</a> 试试。';
                }
            }
        }
        return view('front.tips', ['tips' => $tips, 'message' => $message]);
    }

    /**
     * @function 登录
     * @param Request $request
     * @return array
     */
    public function login(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('front.member.login');
        }
        $this->validate($request, [
            'captcha' => 'required|captcha',
            'username' => 'required',
            'password' => 'required',
        ], [
            'required' => ':attribute不能为空',
            'captcha' => '验证码错误'
        ], [
            'username' => '用户名',
            'password' => '密码',
            'captcha' => '验证码'
        ]);
        $user = User::whereRaw("username=? and password=?", array($request->get('username'), sha1($request->get('password'))))->first();
        if ($user) {
            $request->session()->put(array(
                'uid' => $user->id,
                'uname' => $user->username,
                'ucode' =>  $user->code
            ));
            return result(10000, '登录成功！');
        } else {
            return result(20003, '用户名或密码错误！');
        }
    }

    /**
     * @function 登出
     * @param Request $request
     * @return array
     */
    public function logout(Request $request)
    {
        $request->session()->flush();
        return result(10000, '退出成功！');
    }

    /**
     * @function 获取长居地列表
     * @param Request $request
     * @return string
     */
    public function getPlaces(Request $request)
    {
        return places();
    }

    /**
     * @function 保存用户上传头像原文件并返回裁剪
     * @param Request $request
     * @return array
     */
    public function setHeadPortrait(Request $request)
    {
        $allow_mime_type = array(
            'image/gif',
            'image/png',
            'image/jpeg'
        );
        $head_portrait = $request->file('head_portrait');
        if ($head_portrait->isValid() && in_array($head_portrait->getMimeType(), $allow_mime_type)) {
            $path = $request->file('head_portrait')->store('/temp/head_portrait', 'addressable');
            return result(10000, '/' . $path);
        } else {
            return result(20012, '文件错误！');
        }
    }

    /**
     * @function 更新用户头像
     * @param Request $request
     * @return array
     */
    public function saveHeadPortrait(Request $request)
    {
        $image_p = imagecreatetruecolor(132, 132);
        $image = imagecreatefromjpeg(public_path($request->get('path')));
        imagecopyresampled($image_p, $image, 0, 0, $request->get('x'), $request->get('y'), 132, 132, $request->get('width'), $request->get('height'));
        $path = '/uploads/head_portrait/' . uniqid() . '.png';
        $full_path = public_path($path);
        imagejpeg($image_p, $full_path, 100);
        $user = User::find($request->session()->get('uid'));
        $old_path = $user->avatar;
        $user->avatar = $path;
        if ($user->save()) {
            if (!empty($old_path)) {
                @unlink(public_path($old_path));
            }
            return result(10000, $path);
        } else {
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 展示用户个人信息
     * @param Request $request
     * @param string $opt
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $opt = 'groups')
    {
        $opt = in_array($opt, $this->pageTabs) ? $opt : 'groups';
        $uid = $request->input('uid', $request->session()->get('uid'));
        $user = User::find($uid);
        if ($user) {
            return response()->view('front.member.show', array(
                'user' => $user,
                'opt' => $opt
            ));
        } else {
            abort(404);
        }
    }

    /**
     * @function 更新用户签名
     * @param Request $request
     * @return array
     */
    public function setSignature(Request $request)
    {
        $user = User::find($request->session()->get('uid'));
        $user->signature = $request->get('signature');
        if ($user->save()) {
            return result(10000, '签名已修改！');
        } else {
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 更新用户介绍
     * @param Request $request
     * @return array
     */
    public function setIntroduce(Request $request)
    {
        $user = User::find($request->session()->get('uid'));
        $user->introduce = $request->get('introduce');
        if ($user->save()) {
            return result(10000, '个人介绍已修改！');
        } else {
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 更新用户个人信息
     * @param Request $request
     * @return array
     */
    public function updateMemberInfo(Request $request)
    {
        $user = User::find($request->session()->get('uid'));
        $this->validate($request, [
            'username' => "required|between:3,10|unique:user,username,{$user->id}",
            'sex' => 'required|integer|between:0,2',
            'age' => 'required|integer|between:0,127',
            'place' => 'string',
            'email' => "required|email|unique:user,email,{$user->id}"
        ], [
            'required' => ':attribute不能为空',
            'username.unique' => '用户名已存在',
            'email.unique' => '邮箱已被使用',
            'username.between' => ':attribute最小长度为:min，最大长度为:max',
            'sex.between' => '请输入合法:attribute',
            'age.between' => '请输入合法:attribute',
            'integer' => '请输入合法:attribute',
            'place' => '请输入合法:attribute',
            'email' => ':attribute格式错误'
        ], [
            'username' => '用户名',
            'sex' => '性别',
            'age' => '年龄',
            'place' => '长居地',
            'email' => '邮箱'
        ]);
        $user->username = $request->input('username');
        $user->sex = $request->input('sex');
        $user->age = $request->input('age');
        $user->place = $request->input('place', '');
        if ($user->email != $request->input('email')) {
            $user->confirmation = str_random(16);
            $user->email = $request->input('email');
            $parameters = [
                'username' => $user->username,
                'confirmation' => $user->confirmation,
                'domain' => env('MAIL_APP_URL')
            ];
            $job = (new SendEmail('email.register', $parameters, $user->email, '扬帆小组-账户激活'))->onQueue('email');
            dispatch($job);
            $res = '<br/ >请前往邮箱激活账号';
        } else {
            $res = '';
        }
        if ($user->save()) {
            $request->session('uname', $user->username);
            if (empty($user->confirmation)) {
                return result(10000, '保存成功！');
            } else {
                return result(10001, '保存成功！' . $res);
            }
        } else {
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 发送激活账号邮件
     * @param Request $request
     * @return array
     */
    public function sendEmailForActivate(Request $request)
    {
        $user = User::find($request->session()->get('uid'));
        if (!empty($user->confirmation)) {
            $parameters = [
                'username' => $user->username,
                'confirmation' => $user->confirmation,
                'domain' => env('MAIL_APP_URL')
            ];
            $job = (new SendEmail('email.register', $parameters, $user->email, '扬帆小组-账户激活'))->onQueue('email');
            dispatch($job);
            return result(10000, '请前往邮箱激活账号');
        } else {
            return result(20004, '您的账号已激活，请勿重复操作');
        }
    }

    /**
     * @function 更新用户密码
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('front.member.update_password');
        }
        $user = User::find($request->session()->get('uid'));
        if ($request->has('old_pass')) {
            if (sha1($request->get('old_pass')) != $user->password) {
                return response()->json(array(
                    'old_pass' => array('旧密码错误')
                ), '422');
            }
        } else {
            return response()->json(array(
                'old_pass' => array('旧密码不能为空')
            ), '422');
        }
        $this->validate($request, [
            'new_pass' => 'required|between:8,16',
            're_pass' => 'required|same:new_pass'
        ], [
            'required' => ':attribute不能为空',
            'between' => ':attribute最小长度为:min，最大长度为:max',
            'same' => '两次密码输入不一致'
        ], [
            'new_pass' => '新密码',
            're_pass' => '再次确认'
        ]);
        $user->password = sha1($request->get('new_pass'));
        if ($user->save()) {
            $request->session()->flush();
            return result(10000, '修改成功！<br />请重新登录');
        } else {
            return result(20000, '网络繁忙，请稍后再试！');
        }
    }

    /**
     * @function 发送重置密码邮件
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function resetPassword1(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('front.member.reset_password_1');
        }
        if ($request->has('username')) {
            $user = User::whereRaw("username=?", array($request->input('username')))->first();
            if ($user) {
                if (!empty($user->confirmation)) {
                    return result(20005, '账号未激活，密码不可找回<br/>请前往邮箱点击激活链接或者联系人工客服');
                } else {
                    $parameters = [
                        'username' => $user->username,
                        'confirmation' => $user->code,
                        'domain' => env('MAIL_APP_URL')
                    ];
                    $job = (new SendEmail('email.reset_password', $parameters, $user->email, '扬帆小组-找回密码'))->onQueue('email');
                    dispatch($job);
                    return result(10000, '邮件已发送至您的邮箱，<br/>请根据邮件步骤找密码');
                }
            } else {
                return response()->json(array(
                    'username' => array('用户不存在')
                ), '422');
            }
        } else {
            return response()->json(array(
                'username' => array('用户名不能为空')
            ), '422');
        }
    }

    /**
     * @function 重置用户密码
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function resetPassword2(Request $request)
    {
        if ($request->has('confirmation')) {
            $user = User::whereRaw("code=?", array($request->input('confirmation')))->first();
            if ($user) {
                if ($request->isMethod('GET')) {
                    return view('front.member.reset_password_2', ['confirmation' => $user->confirmation]);
                } elseif ($request->isMethod('POST')) {
                    $this->validate($request, [
                        'new_pass' => 'required|between:8,16',
                        're_pass' => 'required|same:new_pass'
                    ], [
                        'required' => ':attribute不能为空',
                        'between' => ':attribute最小长度为:min，最大长度为:max',
                        'same' => '两次密码输入不一致'
                    ], [
                        'new_pass' => '新密码',
                        're_pass' => '再次确认'
                    ]);
                    $user->password = sha1($request->get('new_pass'));
                    if ($user->save()) {
                        return result(10000, '修改成功！<br />请重新登录');
                    } else {
                        return result(20000, '网络繁忙，请稍后再试！');
                    }
                }
            }
        }
        return view('front.tips', ['tips' => '链接已失效', 'message' => '链接已失效，请您 <a href="/login">登录</a> 试试。']);
    }

    public function topics(Request $request)
    {
        $user = User::find(session('uid'));
        $topics = $user->topics()->orderByDesc('topic.created_at')->paginate(1);
        return view('front.member.topics', array(
            'user'=>$user,
            'topics'=>$topics
        ));
    }
}
