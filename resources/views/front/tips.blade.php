@extends('front.layout')
@section('title')提示@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/tips.css') }}">
@stop
@section('main')
    <div class="page-info">
        <div class="wrapper">
            <span>{{ $tips }}</span>
            <p>{!! $message !!}</p>
        </div>
    </div>
@stop
@section('js_code')@stop