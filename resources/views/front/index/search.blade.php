@extends('front.layout')
@section('title')搜索-{{ request('keyword') }}@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/search.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/search.js') }}"></script>
@stop
@section('main')
    <div class="page-info">
        <div class="wrapper">
            <span>@if($type == 'group')小组@elseif($type == 'member')成员@else讨论@endif搜索:{{ request('keyword') }}</span>
        </div>
    </div>
    <div class="main">
        <div class="wrapper">
            <div class="main-left">
                <ul class="search-type">
                    <li><a href="/search/topic?keyword={{ request('keyword') }}" title=""
                           class="{{ $type == 'topic'?'active':''}}">讨论</a></li>
                    <li><a href="/search/group?keyword={{ request('keyword') }}" title=""
                           class="{{ $type == 'group'?'active':''}}">小组</a></li>
                    <li><a href="/search/member?keyword={{ request('keyword') }}" title=""
                           class="{{ $type == 'member'?'active':''}}">用户</a></li>
                    @if( $type == 'topic' )
                        <li class="opt">
                            <a href="/search/topic?keyword={{ request('keyword') }}" title=""
                               class="{{ request('order', 'default') == 'default'?'active':'' }}">相关度</a>
                            <span>/</span>
                            <a href="/search/topic?order=new&keyword={{ request('keyword') }}" title=""
                               class="{{ request('order', 'default') == 'new'?'active':'' }}">最新发布</a>
                        </li>
                    @endif
                </ul>
                @if($type == 'group')
                    <ul class="group-list">
                        @foreach( $data as $k=>$v )
                            <li><a href="/group/{{ $v->id }}" title=""><img src="{{ $v->icon }}" alt=""></a>
                                <div>
                                    <a href="/group/{{ $v->id }}" title="">{{ $v->name }}</a><br/>
                                    <span>{{ $v->allMember()->count() }}个成员再次聚集</span>
                                    <p>{{ str_limit($v->introduce,200) }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
                @if($type == 'topic')
                    <div class="topic-list">
                        <table>
                            @foreach( $data as $k=>$v )
                                <tr>
                                    <td><a href="/topic/{{ $v->id }}" title="">{{ $v->title }}</a></td>
                                    <td>{{ $v->created_at }}</td>
                                    <td>{{ $v->comments()->count() }}回应</td>
                                    <td><a href="/group/{{ $v->group->id }}" title="">{{ $v->group->name }}</a></td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
                @if($type == 'member')
                    <ul class="member-list">
                        @foreach( $data as $k=>$v )
                            <li><a href="/member?uid={{ $v->id }}" title=""><img src="{{ $v->avatar }}" alt=""></a>
                                <div>
                                    <a href="/member?uid={{ $v->id }}" title="">{{ $v->username }}</a><br/>
                                    <span>{{ $v->fans()->count() }}人关注/{{ empty($v->place)?"保密":$v->place }}</span>
                                    <p>{{ str_limit($v->signature,250) }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <div class="page">
                    {{ $data->links() }}
                </div>
            </div>
            <div class="main-right">
                <a href="#" title="">&gt;对结果不满意？让我们知道</a>
            </div>
        </div>
    </div>
@stop