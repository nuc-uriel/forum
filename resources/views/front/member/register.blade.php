@extends('front.layout')
@section('title')注册@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/register.js') }}"></script>
@stop
@section('navbar')
@stop
@section('main')
    <div class="register_con">
        <div class="l_con fl">
            <a class="reg_logo"><img src="img/logo.png"></a>
            <div class="reg_banner"></div>
        </div>

        <div class="r_con fr">
            <div class="reg_title clearfix">
                <h1>用户注册</h1>
                <a href="/login">登录</a>
            </div>
            <div class="reg_form clearfix">
                <form action="#" method="post" id="reg_form">
                    {{csrf_field()}}
                    <ul>
                        <li>
                            <label>用户名:</label>
                            <input type="text" name="username" id="username">
                            <span class="error_tip">提示信息</span>
                        </li>
                        <li>
                            <label>密码:</label>
                            <input type="password" name="password" id="password">
                            <span class="error_tip">提示信息</span>
                        </li>
                        <li>
                            <label>确认密码:</label>
                            <input type="password" name="cpwd" id="cpwd">
                            <span class="error_tip">提示信息</span>
                        </li>
                        <li>
                            <label>邮箱:</label>
                            <input type="text" name="email" id="email">
                            <span class="error_tip">提示信息</span>
                        </li>
                        <li>
                            <label>验证码:</label>
                            <div class="captcha">
                                <input type="text" name="captcha" id="captcha">
                                <img src="{{captcha_src()}}" alt="" style="cursor: pointer;"
                                     onclick="this.src='{{captcha_src()}}'+Math.random()">
                            </div>
                            <span class="error_tip">提示信息</span>
                        </li>
                        <li class="agreement">
                            <input type="checkbox" name="allow" id="allow" checked="checked">
                            <label>同意”扬帆小组用户使用协议“</label>
                            <span class="error_tip2">提示信息</span>
                        </li>
                        <li class="reg_sub">
                            <input type="submit" value="注 册" name="">
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
@stop