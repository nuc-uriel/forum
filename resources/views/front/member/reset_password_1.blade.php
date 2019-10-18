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
            <span>重置密码-第一步</span>
        </div>
    </div>
    <div class="main">
        <div class="wrapper">
            <div class="main-left">
                <div class="reset-pass">
                    <form action="#" method="post" accept-charset="utf-8" id="reset-pass-1">
                        {{csrf_field()}}
                        <table>
                            <tr>
                                <th>用户名</th>
                                <td><input type="text" name="username" value="" class="username" placeholder="用户名"></td>
                                <td class="error-tip" hidden="">错误提示</td>
                            </tr>
                            <tr>
                                <th></th>
                                <td><input type="submit" name="" value="下一步" class="set-info-go"></td>
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