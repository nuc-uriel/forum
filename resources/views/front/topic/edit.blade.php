@extends('front.layout')
@section('title')编辑主题@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/edit_topic.css') }}">
    {!! we_css() !!}
    {!! we_js() !!}
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/edit_topic.js')}}"></script>
@stop
@section('navbar')
@stop
@section('main')
    <div class="top-bar">
        <div class="wrapper">
            <div class="logo">
                <a href="" title="">扬帆小组</a>
            </div>
            <span>·</span>
            <h2>写小组讨论</h2>
        </div>
        <div class="opt">
            <a href="javascript:void(0);" title="" class="edit" style="display: none;">返回编辑</a>
            <a href="javascript:void(0);" title="" class="preview">预览</a>
            <a href="javascript:void(0);" title="" class="submit-for-form">提交</a>
        </div>
    </div>
    <div class="main">
        <div class="wrapper">
            <a href="/group/{{ $group->id }}" class="group-info">
                <img src="{{ $group->icon }}" alt="">
                <span>{{ $group->name }}</span>
            </a>
            <form action="" id="add-topic-form">
                {{csrf_field()}}
                <input type="hidden" name="gid" value="{{ $group->id }}">
                <input type="hidden" name="tid" value="{{ isset($topic)?$topic->id:'' }}">
                <input type="text" name="title" value="{{ isset($topic)?$topic->title:'' }}" placeholder="添加标题" maxlength="64" class="title-in">
                {!! we_field('wangeditor', 'content', isset($topic)?$topic->content:'<p style="color:#DADADA;">写小组讨论...</p>') !!}
            </form>
            <div class="preview-container" style="display: none;">
                <h3></h3>
                <div class="user-info">
                    <img src="{{ $user->avatar }}" alt="">
                    <a href="/member?uid={{ $user->id }}">{{ $user->username }}</a>
                    <span>{{ date('Y-m-d') }}</span>
                </div>
                <div class="content-show"></div>
            </div>
        </div>
    </div>
@stop
@section('footer')
@stop
@section('js_code')
    @parent
    {!! we_config('wangeditor') !!}
@stop
