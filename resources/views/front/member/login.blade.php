@extends('front.layout')
@section('title')登录@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/login.js') }}"></script>
@stop
@section('navbar')
@stop
@section('main')
    <div class="login_top clearfix">
        <a href="index.html" class="login_logo"><img src="img/logo.png" width="200"></a>
    </div>

    <div class="login_form_bg">
        <div class="login_form_wrap clearfix">
            <div class="login_banner fl"></div>
            <div class="slogan fl">畅所欲言 · 扬帆起航</div>
            <div class="login_form fr">
                <div class="login_title clearfix">
                    <h1>用户登录</h1>
                    <a href="/register">立即注册</a>
                </div>
                <div class="form_input">
                    <form id="login_form" action="#" method="post">
                        {{csrf_field()}}
                        <input type="text" name="username" class="name_input username" placeholder="请输入用户名">
                        <div class="user_error error_tip">输入错误</div>
                        <input type="password" name="password" class="pass_input password" placeholder="请输入密码">
                        <div class="pwd_error error_tip">输入错误</div>
                        <div class="captcha">
                            <input type="text" name="captcha" id="captcha" placeholder="请输入验证码">
                            <img src="{{captcha_src()}}" alt="" style="cursor: pointer;"
                                 onclick="this.src='{{captcha_src()}}'+Math.random()">
                        </div>
                        <div class="captcha_error error_tip">输入错误</div>
                        <div class="more_input clearfix">
                            <input type="checkbox" name="">
                            <label>记住用户名</label>
                            <a href="/member/password/reset/1">忘记密码</a>
                        </div>
                        <input type="submit" name="" value="登录" class="input_submit">
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js_code')@stop