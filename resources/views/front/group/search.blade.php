@extends('front.layout')
@section('title'){{ $group->name }}小组-讨论搜索@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/search_in_group.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/search_in_group.js') }}"></script>
@stop
@section('main')
    <div class="search-info">
        <div class="wrapper">
            <span>{{ $group->name }} 小组讨论搜索：{{ request('keyword') }}</span>
        </div>
    </div>
    <div class="main">
        <div class="wrapper">
            <div class="main-left">
                <div class="search">
                    <form action="" method="get" accept-charset="utf-8">
                        <input type="hidden" name="gid" value="{{ $group->id }}">
                        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="" class="search-in">
                        <input type="submit" name="" value="搜索" class="search-go">
                    </form>
                </div>
                <div class="topic-list">
                    <div class="opt">
                        <a href="?gid={{ $group->id }}&keyword={{ request('keyword') }}" title=""
                           class="{{ request('order', 'default') == 'default'?'active':'' }}">相关度</a>
                        <span>/</span>
                        <a href="?gid={{ $group->id }}&order=new&keyword={{ request('keyword') }}" title=""
                           class="{{ request('order', 'default') == 'new'?'active':'' }}">最新发布</a>
                    </div>
                    <table>
                        @foreach( $topics as $k=>$topic )
                            <tr>
                                <td width="60%"><a href="/topic/{{ $topic->id }}" title="" class="title">{{ $topic->title }}</a></td>
                                <td>{{ $topic->created_at }}</td>
                                <td>{{ $topic->comments()->count() }}回应</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <div class="page">
                    {{ $topics->links() }}
                </div>
            </div>
            <div class="main-right">
                <div>
                    <a href="#" title="">&gt;对结果不满意？让我们知道</a>
                </div>
                <div>
                    <a href="/group/{{$group->id}}" title="">&gt;回到{{$group->name}}</a>
                </div>
            </div>
        </div>
    </div>
@stop