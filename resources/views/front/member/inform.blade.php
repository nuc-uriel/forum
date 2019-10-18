@extends('front.layout')
@section('title'){{ $type_info['name'] }}@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/message.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/inform.js') }}"></script>
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
                    <li><a href="/chat/list" title="">朋友私信</a><span
                                class="tips" {{ $message_count == 0?'hidden=""':'' }}>{{ $message_count }}</span></li>
                    <li><a href="/inform/list" title="" class="active">系统通知</a><span
                                class="tips" {{ $inform_count == 0?'hidden=""':'' }}>{{ $inform_count }}</span></li>
                </ul>
                <div class="records">
                    <span>{{ $type_info['name'] }}</span>
                    <a href="javascript:history.back(-1)" title="">返回</a>
                    <ul id="records">
                        @foreach( $user->informs()->where('status', '!=', 4)->whereBetween('type', $type_info['range'])->get() as $k=>$msg )
                            <li class="friend">
                                <img src="{{ asset('img/'. $type .'.png') }}" alt="">
                                <div>
                                    <p>{!! $msg->content !!}</p>
                                    <div class="date">{{ $msg->created_at }}</div>
                                    @if($msg->is_dispose==1)
                                        @if($msg->disposer_id!=0)
                                        <div class="res"><a href="/member/{{ $msg->disposer_id }}">{{ $msg->disposer->username }}</a>已{{ $msg->status==2?'同意':'拒绝' }}</div>
                                        @else
                                            <div class="res"><a href="/inform/pass/{{ $msg->code }}" class="dispose">同意</a><span>|</span><a href="/inform/refuse/{{ $msg->code }}" class="dispose">拒绝</a></div>
                                        @endif
                                    @endif
                                </div>
                            </li>
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
                    <img src="{{ $user->avatar }}" alt="">
                    <div>
                        <a href="/member" title="">我的个人主页</a>
                        <div>
                            <a href="/member/issue" title="">发起({{ $user->topicsAsSender()->count() }})</a>
                            <span>|</span>
                            <a href="/member/respond" title="">回应({{ $user->topicsAsCommenter()->count() }})</a>
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