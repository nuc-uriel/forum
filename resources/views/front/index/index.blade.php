@extends('front.layout')
@section('title')首页@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/index.js') }}"></script>
@stop
@section('main')
    <div class="tab-info">
        <div class="wrapper">
            <span class="tab-name">{{ $type['name'] }}</span>
            <span class="tab-explain">{{ $type['introduce'] }}</span>
        </div>
    </div>
    <div class="main">
        <div class="wrapper">
            <div class="articles">
                @foreach($topics as $k=>$topic)
                    <div class="item">
                        <div class="like">
                            <span>{{ $topic->likes()->count() }}</span><span>喜欢</span>
                        </div>
                        <div class="article">
                            <a href="/topic/{{ $topic->id }}" title="" class="title"><h3>{{ $topic->title }}</h3></a>
                            <a href="/topic/{{ $topic->id }}">
                                <div class="content">
                                    @if($topic->firstImg())
                                        <img src="{{ $topic->firstImg() }}" alt="">
                                    @endif
                                    <p>{{ str_limit(strip_tags($topic->content), 200) }}</p>
                                </div>
                                <div class="info">
                                    <span>来自</span><a href="/group/{{ $topic->group->id }}">{{ $topic->group->name }}小组</a><span
                                            class="time">{{ $topic->created_at }}</span>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
                <div class="page">
                    {{ $topics->links() }}
                </div>
            </div>
            <div class="groups">
                @if(isset($user))
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
                @endif
                <h2>值的加入的小组</h2>
                    @foreach($groups as $k=>$group)
                        <div class="group">
                            <img src="{{ $group->icon }}" alt="">
                            <div class="info">
                                <a href="/group/{{ $group->id }}" class="group-name">{{ $group->name }}小组</a>
                                <div class="into-group">
                                    <span>{{ $group->allMember()->count() }}个成员</span>
                                    <a href="/group/{{ $group->id }}">+加入小组</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                <div style="margin-top: 20px;">
                    <a href="/group/build" class="build-group">+申请创建小组</a>
                </div>
            </div>
        </div>
    </div>
@stop