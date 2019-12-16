@extends('front.layout')
@section('title')私信列表@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/message.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/chat_list.js') }}"></script>
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
                    },
                    end: function () {
                        window.location.reload();
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
                        end: function () {
                            window.location.reload();
                        }
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
                        content: e.inform.content,
                        end: function () {
                            window.location.reload();
                        }
                    });
                }
            });
        });
    </script>
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
                <ul class="opt">
                    <li><a href="/chat/list" title="" class="{{ isset($messages)?'active':'' }}">朋友私信</a><span
                                class="tips" {{ $message_count == 0?'hidden=""':'' }}>{{ $message_count }}</span></li>
                    <li><a href="/inform/list" title="" class="{{ isset($messages)?'':'active' }}">系统通知</a><span
                                class="tips" {{ $inform_count == 0?'hidden=""':'' }}>{{ $inform_count }}</span></li>
                </ul>
                <ul class="message-list">
                    @if(isset($messages))
                        @foreach($messages as $k=>$msg)
                            <li href="/chat/{{ $msg->uf_id==session('uid')?$msg->receiver->id:$msg->sender->id }}">
                                <img src="{{ $msg->uf_id==session('uid')?$msg->receiver->avatar:$msg->sender->avatar }}"
                                     alt="">
                                <span class="tips" {{ $msg->count == 0?'hidden=""':'' }}>{{ $msg->count }}</span>
                                <div>
                                    <div>
                                        <a href="/member/{{ $msg->uf_id==session('uid')?$msg->receiver->id:$msg->sender->id }}" title="" class="username">{{ $msg->sender->username }}</a>
                                        <span class="date">{{ $msg->created_at }}</span>
                                    </div>
                                    <div>
                                        <span class="content">{!! $msg->content !!}</span>
                                        <a href="javascript:void(0);" url="/chat/del/{{ $msg->uf_id==session('uid')?$msg->receiver->id:$msg->sender->id }}" title="" class="del-message">删除对话</a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    @else
                        @if($user->informs->where('type', '>=', 0)->where('type', '<=', 199)->where('status', '!=', 4)->count() > 0 )
                            <li href="/inform/wait_pass">
                                <img src="{{ asset('img/wait_pass.png') }}"
                                     alt="">
                                <span class="tips" {{ $user->informs->where('type', '>=', 0)->where('type', '<=', 199)->where('status', 0)->count()==0?'hidden=""':'' }}>{{ $user->informs->where('type', '>=', 0)->where('type', '<=', 199)->where('status', 0)->count() }}</span>
                                <div>
                                    <div>
                                        <a href="/inform/wait_pass" title="" class="username">待处理</a>
                                        <span class="date">{{ $user->informs->where('type', '>=', 0)->where('type', '<=', 199)->where('status', '!=', 4)->sortByDesc('created_at')->first()->created_at }}</span>
                                    </div>
                                    <div>
                                        <span class="content">{!! $user->informs->where('type', '>=', 0)->where('type', '<=', 199)->where('status', '!=', 4)->sortByDesc('created_at')->first()->content !!}</span>
                                        <a href="javascript:void(0);" url="/inform/del/wait_pass" title="" class="del-message">删除对话</a>
                                    </div>
                                </div>
                            </li>
                        @endif
                        @if($user->informs->where('type', '>=', 200)->where('type', '<=', 299)->where('status', '!=', 4)->count() > 0)
                            <li href="/inform/notify">
                                <img src="{{ asset('img/notify.png') }}"
                                     alt="">
                                <span class="tips" {{ $user->informs->where('type', '>=', 200)->where('type', '<=', 299)->where('status', 0)->count()==0?'hidden=""':'' }}>{{ $user->informs->where('type', '>=', 200)->where('type', '<=', 299)->where('status', 0)->count() }}</span>
                                <div>
                                    <div>
                                        <a href="/inform/notify" title="" class="username">通知</a>
                                        <span class="date">{{ $user->informs->where('type', '>=', 200)->where('type', '<=', 299)->where('status', '!=', 4)->sortByDesc('created_at')->first()->created_at }}</span>
                                    </div>
                                    <div>
                                        <span class="content">{!! $user->informs->where('type', '>=', 200)->where('type', '<=', 299)->where('status', '!=', 4)->sortByDesc('created_at')->first()->content !!}</span>
                                        <a  href="javascript:void(0);" url="/inform/del/notify" title="" class="del-message">删除对话</a>
                                    </div>
                                </div>
                            </li>
                        @endif
                        @if($user->informs->where('type', '>=', 300)->where('type', '<=', 399)->where('status', '!=', 4)->count() > 0)
                            <li href="/inform/reply">
                                <img src="{{ asset('img/reply.png') }}"
                                     alt="">
                                <span class="tips" {{ $user->informs->where('type', '>=', 300)->where('type', '<=', 399)->where('status', 0)->count()==0?'hidden=""':'' }}>{{ $user->informs->where('type', '>=', 300)->where('type', '<=', 399)->where('status', 0)->count() }}</span>
                                <div>
                                    <div>
                                        <a href="/inform/reply" title="" class="username">回复</a>
                                        <span class="date">{{ $user->informs->where('type', '>=', 300)->where('type', '<=', 399)->where('status', '!=', 4)->sortByDesc('created_at')->first()->created_at }}</span>
                                    </div>
                                    <div>
                                        <span class="content">{!! $user->informs->where('type', '>=', 300)->where('type', '<=', 399)->where('status', '!=', 4)->sortByDesc('created_at')->first()->content !!}</span>
                                        <a  href="javascript:void(0);" url="/inform/del/reply" title="" class="del-message">删除对话</a>
                                    </div>
                                </div>
                            </li>
                        @endif
                        @if($user->informs->where('type', '>=', 400)->where('type', '<=', 499)->where('status', '!=', 4)->count() > 0)
                            <li href="/inform/like">
                                <img src="{{ asset('img/like.png') }}"
                                     alt="">
                                <span class="tips" {{ $user->informs->where('type', '>=', 400)->where('type', '<=', 499)->where('status', 0)->count()==0?'hidden=""':'' }}>{{ $user->informs->where('type', '>=', 400)->where('type', '<=', 499)->where('status', 0)->count() }}</span>
                                <div>
                                    <div>
                                        <a href="/inform/like" title="" class="username">赞</a>
                                        <span class="date">{{ $user->informs->where('type', '>=', 400)->where('type', '<=', 499)->where('status', '!=', 4)->sortByDesc('created_at')->first()->created_at }}</span>
                                    </div>
                                    <div>
                                        <span class="content">{!! $user->informs->where('type', '>=', 400)->where('type', '<=', 499)->where('status', '!=', 4)->sortByDesc('created_at')->first()->content !!}</span>
                                        <a  href="javascript:void(0);" url="/inform/del/like" title="" class="del-message">删除对话</a>
                                    </div>
                                </div>
                            </li>
                        @endif
                        @if($user->informs->where('type', '>=', 500)->where('type', '<=', 599)->where('status', '!=', 4)->count() > 0)
                            <li href="/inform/follow">
                                <img src="{{ asset('img/follow.png') }}"
                                     alt="">
                                <span class="tips" {{ $user->informs->where('type', '>=', 500)->where('type', '<=', 599)->where('status', 0)->count()==0?'hidden=""':'' }}>{{ $user->informs->where('type', '>=', 500)->where('type', '<=', 599)->where('status', 0)->count() }}</span>
                                <div>
                                    <div>
                                        <a href="/inform/follow" title="" class="username">关注</a>
                                        <span class="date">{{ $user->informs->where('type', '>=', 500)->where('type', '<=', 599)->where('status', '!=', 4)->sortByDesc('created_at')->first()->created_at }}</span>
                                    </div>
                                    <div>
                                        <span class="content">{!! $user->informs->where('type', '>=', 500)->where('type', '<=', 599)->where('status', '!=', 4)->sortByDesc('created_at')->first()->content !!}</span>
                                        <a href="javascript:void(0);" url="/inform/del/follow" title="" class="del-message">删除对话</a>
                                    </div>
                                </div>
                            </li>
                        @endif
                    @endif
                </ul>
                @if(isset($messages))
                    <div class="page">
                        {{ $messages->links() }}
                    </div>
                @endif
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

@show