@extends('front.layout')
@section('title')我的小组@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/member_topics.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/member_topics.js') }}"></script>
@stop
@section('main')
    <div class="page-info">
        <div class="wrapper">
            <span>我的小组讨论</span>
        </div>
    </div>
    <div class="main">
        <div class="wrapper">
            <div class="main-left">
                <div class="topics">
                    <div class="opt">
                    </div>
                    <table>
                        @foreach( $topics as $k=>$topic )
                            <tr>
                                <td width="55%">
                                    <a href="/topic/{{ $topic->id }}" title="" class="title">{{ $topic->title }}</a>
                                </td>
                                <td>{{ $topic->comments()->count() }}回应</td>
                                <td>{{ $topic->created_at }}</td>
                                <td width="20%"><a href="/group/{{ $topic->group->id }}" title="">{{ $topic->group->name }}</a></td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <div class="page">
                    {{ $topics->links() }}
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