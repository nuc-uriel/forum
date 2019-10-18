<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="code" content="{{ session('ucode') }}">
    @yield('meta')
    <title>扬帆小组-@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    @yield('css')
    <script type="application/javascript" src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script type="application/javascript" src="{{ asset('js/layer/layer.js') }}"></script>
@if(session('uid'))
        <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
    @endif
</head>
<body>
<div id="app">
    @section('navbar')
        <div class="navbar">
            <div class="wrapper">
                <div class="logo">
                    <a href="/" title="">扬帆小组</a>
                </div>
                <div class="tabs">
                    <p hidden="">{{ $type=\App\GroupType::where('status', \App\GroupType::STATUS_NORMAL)->get() }}</p>
                    <ul>
                        @if( !empty(session('uid', '')) )
                            <li><a href="/member/topics" title="">我的小组</a></li>
                        @endif
                        <li><a href="/index" title="">精选</a></li>
                        @foreach( $type as $k=>$v )
                            <li><a href="/index?type={{$v->id}}" title="">{{ $v->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="search">
                    <form action="{{ request()->route()->getActionName() == 'App\Http\Controllers\IndexController@search'?"":"/search" }}"
                          method="get" accept-charset="utf-8">
                        <input class="search-in" type="text" name="keyword" value="{{ request('keyword') }}"
                               placeholder="小组、话题">
                        <input class="search-go" type="submit" name="" value="">
                    </form>
                </div>
                <div class="login">
                    @if(!session('uid'))
                        <a href="/login">登录</a>
                        <span>/</span>
                        <a href="/register">注册</a>
                    @else
                        <a href="/member">{{ session('uname') }}</a>
                        <span>|</span>
                        <a href="/chat/list">消息</a>
                        <span>|</span>
                        <a href="javascript:void(0);" class="logout">退出</a>
                    @endif
                </div>
            </div>
        </div>
    @show
    @section('main')
    @show
    @section('footer')
        <div class="footer">
            <div class="links">
                <a href="#">关于我们</a>
                <span>|</span>
                <a href="#">联系我们</a>
                <span>|</span>
                <a href="#">招聘人才</a>
                <span>|</span>
                <a href="#">友情链接</a>
            </div>
            <p>CopyRight &copy; 2019 凯凯科技 All Rights Reserved
                <br/> 电话：010-****888 京ICP备*******8号&quot</p>
        </div>
    @show
</div>
</body>
@if(session('uid'))
    <script src="{{ asset('js/app.js') }}"></script>
@endif
@yield('js')
@section('js_code')
    @if(session('uid'))
        <script type="application/javascript" src="{{ asset('js/main.js') }}"></script>
    @endif
@show
<script type="application/javascript">
    $('.report').click(function () {
        type = $(this).data('type');
        target = $(this).data('target');
        layer.prompt({
            formType: 2,
            title: '举报',
            resize: false,
            area: ['400px', '150px'] //自定义文本域宽高
        }, function (value, index, elem) {
            data = {
                type: type,
                target: target,
                content: value
            };
            $.ajax({
                url: '/report',
                type: 'POST',
                data: data,
                dataType: "json",
                success: function (data) {
                    if (data.status === 10000) {
                        layer.msg(data.res, {icon: 1});
                    } else {
                        layer.alert(data.res, {
                            title: '提示',
                            skin: 'layui-layer-lan',
                            closeBtn: 0,
                            anim: 6,
                            icon: 0
                        });
                    }
                },
                error: function (data) {
                    layer.alert('网络繁忙，请稍后再试！', {
                        title: '提示',
                        skin: 'layui-layer-lan',
                        closeBtn: 0,
                        anim: 6,
                        icon: 0
                    });
                },
                complete: function () {
                    layer.close(index);
                }
            });
        });
    });
</script>
</html>