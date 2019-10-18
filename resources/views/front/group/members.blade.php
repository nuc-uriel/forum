@extends('front.layout')
@section('title')管理员@stop
@section('meta')
    <meta name="gid" content="{{ $group->id }}"/>
@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/member_list.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/topic_list.js') }}"></script>
@stop
@section('main')
    <div class="page-info">
        <div class="wrapper">
            <span>小组成员</span>
        </div>
    </div>
    <div class="main">
        <div class="wrapper">
            <div class="main-left">
                @if( !empty(request('name', '')) )
                    <div class="member_list">
                        <h4 class="role">搜索结果：{{ request('name') }}</h4>
                        <ul class="list">
                            @foreach($group->allMember()->where('username', 'like', '%'.request('name').'%')->get() as $k=>$member)
                                <li>
                                    <a href="/member/{{ $member->id }}" title=""><img src="{{ $member->avatar }}" alt=""></a>
                                    <a href="/member/{{ $member->id }}" title="" class="username">{{ $member->username }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="member_list">
                        <h4 class="role">组长</h4>
                        <ul class="list">
                            @foreach( $group->leader as $k=>$member )
                                <li>
                                    <a href="/member/{{ $member->id }}" title=""><img src="{{ $member->avatar }}" alt=""></a>
                                    <a href="/member/{{ $member->id }}" title="" class="username">{{ $member->username }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @if(!$group->admin->isEmpty())
                        <div class="member_list">
                            <h4 class="role">{{ empty($group->admin_as)?"管理员":$group->admin_as }}</h4>
                            <ul class="list">
                                @foreach( $group->admin as $k=>$member )
                                    <li>
                                        <a href="/member/{{ $member->id }}" title=""><img src="{{ $member->avatar }}" alt=""></a>
                                        <a href="/member/{{ $member->id }}" title="" class="username">{{ $member->username }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if(!$group->member->isEmpty())
                        <div class="member_list">
                            <h4 class="role">{{ empty($group->member_as)?"组员":$group->member_as }}</h4>
                            <ul class="list">
                                @foreach( $group->member as $k=>$member )
                                    <li>
                                        <a href="/member/{{ $member->id }}" title=""><img src="{{ $member->avatar }}" alt=""></a>
                                        <a href="/member/{{ $member->id }}" title="" class="username">{{ $member->username }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    @endif
                @endif
            </div>
            <div class="main-right">
                <div class="group-info">
                    <div class="group-top">
                        <img src="{{ $group->icon }}" alt="">
                        <div>
                            <a href="/group/{{$group->id}}" title="">{{$group->name}}</a>
                        </div>
                    </div>
                    <div class="group-bottom">
                        <p><span>{{$group->allMember()->count()}}</span>人聚集在这个小组</p>
                        @if(session('uid') && $group->allMember->find(session('uid')))
                            @if($group->allMember->find(session('uid'))->pivot->role == 0)
                                <p>我是这个小组的成员</p>
                            @elseif($group->allMember->find(session('uid'))->pivot->role == 1)
                                <p>我是这个小组的管理员</p>
                            @elseif($group->allMember->find(session('uid'))->pivot->role == 2)
                                <p>我是这个小组的组长</p>
                            @endif
                        @else
                            <a href="javascript:void(0);" title="" class="join-group">申请加入小组</a>
                        @endif
                    </div>
                </div>
                <h4 class="">小组成员搜索</h4>
                <div class="member-search">
                    <form action="" method="get" accept-charset="utf-8">
                        <input type="hidden" name="gid" value="{{ $group->id }}">
                        <input type="text" name="name" value="{{ request('name', '') }}" placeholder="名号" class="search-in">
                        <input type="submit" name="" value="搜索成员" class="search-go">
                    </form>
                </div>
                <div class="opt">
                    <a href="/group/{{$group->id}}" title="">&gt;回到{{ $group->name }}</a>
                </div>
            </div>
        </div>
    </div>
@stop