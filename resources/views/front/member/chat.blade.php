@extends('front.layout')
@section('meta')
    <meta name="uid" content="{{ $friend->id }}"/>
    <meta name="uavatar" content="{{ $friend->avatar }}"/>
@stop
@section('title'){{ $friend->username }}私信@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/message.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/chat.js') }}"></script>
    <script type="application/javascript" src="{{ asset('js/jquery.nicescroll.min.js') }}"></script>
@stop
@section('main')
    <div class="page-info">
        <div class="wrapper">
            <span>我的消息</span>
        </div>
    </div>
    <div class="main">
        <div class="wrapper">
            <div class="main-left">
                <ul class="opt" hidden="">
                    <li><a href="/chat/list" title="" class="active">朋友私信</a><span
                                class="tips" {{ $message_count == 0?'hidden=""':'' }}>{{ $message_count }}</span></li>
                    <li><a href="/inform/list" title="">系统通知</a><span
                                class="tips" {{ $inform_count == 0?'hidden=""':'' }}>{{ $inform_count }}</span></li>
                </ul>
                <div class="records">
                    <span>来自{{ $friend->username }}的私信</span>
                    <a href="javascript:history.back(-1)" title="">返回</a>
                    <ul id="records">
                        @foreach( $myself->getMessageForOne($friend->id) as $k=>$msg )
                            @if($msg->uf_id == $friend->id)
                                <li class="friend">
                                    <img src="{{ $friend->avatar }}" alt="">
                                    <div>
                                        <p>{{ $msg->content }}</p>
                                        <div class="date">{{ $msg->created_at }}</div>
                                    </div>
                                </li>
                            @else
                                <li class="myself">
                                    <div>
                                        <p>{{ $msg->content }}</p>
                                        <div class="date">{{ $msg->created_at }}</div>
                                    </div>
                                    <img src="{{ $myself->avatar }}" alt="">
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    <form action="#" method="post" accept-charset="utf-8" id="send-msg">
                        <input type="text" name="content" value="" placeholder="请输入聊天内容" class="say-in">
                        <input type="submit" name="" value="发送" class="say-go">
                    </form>
                </div>
            </div>
            <div class="main-right">
                <div class="user-info">
                    <img src="{{ $myself->avatar }}" alt="">
                    <div>
                        <a href="/member" title="">我的个人主页</a>
                        <div>
                            <a href="/member/issue" title="">发起({{ $myself->topicsAsSender()->count() }})</a>
                            <span>|</span>
                            <a href="/member/respond" title="">回应({{ $myself->topicsAsCommenter()->count() }})</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_code')
    <script>
        $(function () {
            chat_channel = 'chat.' + $('meta[name=code]').attr('content');
            window.Echo.private(chat_channel).listen('Chat', (e) => {
                uid = $('meta[name=uid]').attr('content');
                avatar = $('meta[name=uavatar]').attr('content');
                if (e.message.uf_id == uid) {
                    li = $('<li class="friend"><img src="' + avatar + '" alt=""><div><p>' + e.message.content + '</p><div class="date">' + e.message.created_at + '</div></div></li>');
                    $('#records').append(li);
                    setTimeout(function () {
                        $("#records").getNiceScroll(0).doScrollTop(($("#records li").length + 10) * 100);
                    }, 100);
                    $.ajax({
                        url: "/chat/read?mid=" + e.message.id,
                        type: 'GET'
                    });
                } else {
                    layer.open({
                        type: 1,
                        title: '来自' + e.user.username,
                        shade: false,
                        skin: 'layer-inform',
                        area: ['340px', '215px'],
                        offset: 'rb', //右下角弹出
                        time: 10000, //10秒后自动关闭
                        anim: 2,
                        resize: false,
                        content: e.message.content,
                        btn: ['查看', '忽略'],
                        yes: function (index, layero) {
                            $(location).prop('href', '/chat/'+e.user.id);
                            layer.close(index);
                        },
                        btn2: function (index, layero) {
                            layer.close(index);
                        }
                    });
                }
            });
            inform_channel = 'inform.' + $('meta[name=code]').attr('content');
            window.Echo.private(inform_channel).listen('SystemInform', (e) => {
                if (e.inform.is_dispose === 1) {
                    layer.open({
                        type: 1,
                        title: '系统通知',
                        shade: false,
                        skin: 'layer-inform',
                        area: ['340px', '215px'],
                        offset: 'rb', //右下角弹出
                        time: 10000, //10秒后自动关闭
                        anim: 2,
                        resize: false,
                        content: e.inform.content,
                        btn: ['同意', '拒绝', '忽略'],
                        yes: function (index, layero) {
                            $.ajax({
                                url: "/inform/pass/" + e.inform.code,
                                type: 'GET',
                                dataType: "json",
                                success: function (data) {
                                    if (data.status === 10000) {
                                    } else if (data.status === 20001) {
                                        layer.alert(data.res, {
                                                title: '提示',
                                                skin: 'layui-layer-lan',
                                                closeBtn: 0,
                                                anim: 6,
                                                icon: 0
                                            },
                                            function () {
                                                $(location).prop('href', '/login');
                                            });
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
                                }
                            });
                            layer.close(index);
                        },
                        btn2: function (index, layero) {
                            $.ajax({
                                url: "/inform/refuse/" + e.inform.code,
                                type: 'GET',
                                dataType: "json",
                                success: function (data) {
                                    if (data.status === 10000) {
                                    } else if (data.status === 20001) {
                                        layer.alert(data.res, {
                                                title: '提示',
                                                skin: 'layui-layer-lan',
                                                closeBtn: 0,
                                                anim: 6,
                                                icon: 0
                                            },
                                            function () {
                                                $(location).prop('href', '/login');
                                            });
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
                            });
                            layer.close(index);
                        },
                        btn3: function (index, layero) {
                            layer.close(index);
                        },
                    });
                } else {
                    layer.open({
                        type: 1,
                        title: '系统通知',
                        shade: false,
                        skin: 'layer-inform',
                        area: ['340px', '215px'],
                        offset: 'rb', //右下角弹出
                        time: 10000, //10秒后自动关闭
                        anim: 2,
                        resize: false,
                        content: e.inform.content
                    });
                }
            });
        });
    </script>
@show