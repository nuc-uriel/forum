@extends('front.layout')
@section('meta')
    <meta name="gid" content="{{ $group->id }}"/>
@stop
@section('title'){{ $group->name }}小组-更多讨论@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/topic_list.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/topic_list.js') }}"></script>
@stop
@section('main')
    <div class="page-info">
        <div class="wrapper">
            <span>更多小组讨论</span>
        </div>
    </div>
    <div class="main">
        <div class="wrapper">
            <div class="main-left">
                <div class="topics">
                    <div class="opt">
                        <span class="placeholder"></span>
                        <a href="/topic/add?gid={{ $group->id }}" title="" class="add-topic" {{ $group->allMember->find(session('uid'))?'':'hidden=""' }}>+ 发言</a>
                    </div>
                    <table>
                        <thead>
                        <tr>
                            <th width="55%">讨论</th>
                            <th width="15%">作者</th>
                            <th>回应</th>
                            <th>最后回应</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach( $topics as $k=>$topic )
                            <tr>
                                <td>
                                    @if( $topic->is_top == \App\Topic::IS_TOP_TRUE )
                                        <img src="/img/stick.gif" alt="">
                                    @endif
                                    <a href="/topic/{{ $topic->id }}" title="" class="title">{{ $topic->title }}</a>
                                </td>
                                <td><a href="/member?uid={{ $topic->creator->id }}" title="">{{ $topic->creator->username }}</a></td>
                                <td>{{ $topic->comments_count }}</td>
                                <td>{{ $topic->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="page">
                    {{ $topics->links() }}
                </div>
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
            </div>
        </div>
    </div>
@stop