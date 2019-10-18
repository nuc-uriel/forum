@extends('front.layout')
@section('meta')
    <meta name="gid" content="{{ $group->id }}"/>
@stop
@section('title'){{ $group->name }}小组@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/group.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/group.js') }}"></script>
    <script type="application/javascript" src="{{ asset('js/get_url_param.js') }}"></script>
@stop
@section('main')
    <div class="group-info">
        <div class="wrapper">
            <img src="{{ $group->icon }}" alt="">
            <span>{{ $group->name }}</span>
            <div>
                @if(session('uid') && $group->allMember->find(session('uid')))
                    @if($group->allMember->find(session('uid'))->pivot->role == 0)
                        <span>我是这个小组的成员&gt;</span>
                        <a href="javascript:void(0);" class="quit-group">退出小组</a>
                    @elseif($group->allMember->find(session('uid'))->pivot->role == 1)
                        <span>我是这个小组的管理员&gt;</span>
                        <a href="javascript:void(0);" class="quit-group">退出小组</a>
                    @elseif($group->allMember->find(session('uid'))->pivot->role == 2)
                        <span>我是这个小组的组长&gt;</span>
                    @endif
                @else
                    <a href="javascript:void(0);" title="" class="join-group">加入小组</a>
                @endif
            </div>
        </div>
    </div>
    <div class="main">
        <div class="wrapper">
            <div class="main-left">
                <div class="group-introduce">
                    <span>创建于{{ $group->created_at->format('Y-m-d') }}</span>
                    <span>组长：</span><a href="/member?uid={{ $group->leader->first()->id }}"
                                       title="">{{ $group->leader->first()->username }}</a>
                    <p>{{ $group->introduce }}</p>
                    <div class="group-tabs">
                        @if($group->labels->count() != 0)
                            <span>小组标签</span>
                            <ul>
                                @foreach( $group->labels as $k=>$label )
                                    <li><a href="/search/group?keyword={{ $label->name }}" title="" target="_blank">{{ $label->name }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                <div class="topics">
                    <div class="opt">
                        <a href="?range=new" title="">最近讨论</a>
                        <span>/</span>
                        <a href="?range=hot" title="">最热讨论</a>
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
                <div class="search-more">
                    <form action="/group/search" method="get" accept-charset="utf-8">
                        <input type="hidden" name="gid" value="{{ $group->id }}">
                        <input type="text" name="keyword" placeholder="搜索本组讨论" class="search-in"><input type="submit" name=""
                                                                                                 value=""
                                                                                                 class="search-go">
                    </form>
                    <div>
                        <a href="/group/topics?gid={{ $group->id }}" title="">&gt;更多小组讨论</a>
                    </div>
                </div>
            </div>
            <div class="main-right">
                <div class="friendship">
                    <h4>友情小组</h4>
                    <ul>
                        @foreach( $group->friendship() as $k=>$fg )
                            <li><img src="{{ $fg->icon }}" alt=""><span><a href="/group/{{ $fg->id }}"
                                                                           title="">{{ $fg->name }}</a>({{ $fg->allMember->count() }})</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="lately-added">
                    <h4>最近加入</h4>
                    <ul>
                        @foreach( $group->allMember->sortByDESC('pivot_created_at')->take(8) as $k=>$member )
                            <li><img src="{{ $member->avatar }}" alt=""><a href="/member?uid={{ $member->id }}"
                                                                           title="">{{ $member->username }}</a></li>
                        @endforeach
                    </ul>
                    <a href="/group/members?gid={{$group->id}}" title=""
                       class="more-member">&gt;浏览所有{{ empty($group->member_as)?'成员':$group->member_as }}
                        ({{ $group->allMember->count() }})</a>
                    @if($group->leader()->find(session('uid')) or $group->admin()->find(session('uid')))
                    <br/><a href="/group/edit/member-edit?gid={{$group->id}}" title=""
                       class="more-member">&gt;成员管理</a>
                    <br/ ><a href="/group/edit?gid={{$group->id}}" title=""
                       class="more-member">&gt;小组管理</a>
                        @endif
                </div>
                <div class="recommend" hidden="">
                    <h4>这个小组的程序猿也喜欢去</h4>
                    <ul>
                        <li><img src="img/提莫.jpg" alt=""><span><a href="#" title="">推荐一号</a>(233)</span></li>
                        <li><img src="img/提莫.jpg" alt=""><span><a href="#" title="">推荐一号</a>(233)</span></li>
                        <li><img src="img/提莫.jpg" alt=""><span><a href="#" title="">好友一号</a>(233)</span></li>
                        <li><img src="img/提莫.jpg" alt=""><span><a href="#" title="">推荐一号</a>(233)</span></li>
                        <li><img src="img/提莫.jpg" alt=""><span><a href="#" title="">推荐一号</a>(233)</span></li>
                        <li><img src="img/提莫.jpg" alt=""><span><a href="#" title="">推荐一号</a>(233)</span></li>
                        <li><img src="img/提莫.jpg" alt=""><span><a href="#" title="">推荐一号</a>(233)</span></li>
                        <li><img src="img/提莫.jpg" alt=""><span><a href="#" title="">好友一号</a>(233)</span></li>
                    </ul>
                </div>
                <a href="#" title="" class="report" data-type="1" data-target="{{$group->id}}">&gt;举报不良信息</a>
            </div>
        </div>
    </div>
@stop