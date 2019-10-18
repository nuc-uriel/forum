@extends('front.layout')
@section('title')重置密码@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/reset_password.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/reset_password.js') }}"></script>
@stop
@section('main')
    <div class="page-info">
        <div class="wrapper">
            <span>重置密码-第二步</span>
        </div>
    </div>
    <div class="main">
        <div class="wrapper">
            <div class="main-left">
                <div class="reset-pass">
                    <form action="#" method="post" accept-charset="utf-8" id="reset-pass-2">
                        {{csrf_field()}}
                        <input type="hidden" name="confirmation" value="{{ $confirmation }}">
                        <table>
                            <tr>
                                <th>新密码</th>
                                <td><input type="password" name="new_pass" value="" class="new_pass" placeholder="新密码(最少8位)"></td>
                                <td class="error-tip" hidden="">错误提示</td>
                            </tr>
                            <tr>
                                <th>再次确认</th>
                                <td><input type="password" name="re_pass" value="" class="re_pass" placeholder="再次确认"></td>
                                <td class="error-tip" hidden="">错误提示</td>
                            </tr>
                            <tr>
                                <th></th>
                                <td><input type="submit" name="" value="保存并登录" class="set-info-go"></td>
                                <td></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
            <div class="main-right"></div>
        </div>
    </div>
@stop
@section('js_code')@stop