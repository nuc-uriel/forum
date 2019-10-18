@extends('front.layout')
@section('meta')
    <meta name="tid" content="{{ $topic->id }}"/>
@stop
@section('title'){{ $topic->title }}@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/topic.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/autosize.min.js')}}"></script>
    <script type="application/javascript" src="{{ asset('js/topic.js') }}"></script>
@stop
@section('main')
    <div class="title">
        <div class="wrapper">
            <span>{{ $topic->title }}</span>
        </div>
    </div>
    <div class="main">
        <div class="wrapper">
            <div class="topic">
                <div class="topic-info">
                    <img src="{{ $topic->creator->avatar }}" alt="" class="head-portrait">
                    <div class="topic-right">
                        <div class="author">
                            <span>来自</span><a
                                    href="/member?uid={{ $topic->creator->id }}">{{ $topic->creator->username }}</a><span>{{ empty($topic->creator->signature)?'':'('.str_limit($topic->creator->signature, 30).')' }}</span>
                            <span class="time">{{ $topic->created_at }}</span>
                        </div>
                        <div class="content">
                            {!! $topic->content !!}
                        </div>
                        <a href="#" title="" class="report" data-type="2" data-target="{{$topic->id}}">举报</a>
                        <ul class="topic-opt" id="topic-opt">
                            @if($topic->group->leader->find(session('uid')) or $topic->group->admin->find(session('uid')))
                                <li><a href="javascript:void(0);" title="" class="topic-admin-opt"
                                       url="/topic/is_top/{{ $topic->is_top == \App\Topic::IS_TOP_FALSE?\App\Topic::IS_TOP_TRUE:\App\Topic::IS_TOP_FALSE }}">{{ $topic->is_top == \App\Topic::IS_TOP_FALSE?"置顶":"取消置顶" }}</a>
                                </li>
                                <li class="separator">|</li>
                                <li><a href="javascript:void(0);" title="" class="topic-admin-opt"
                                       url="/topic/can_comment/{{ $topic->can_comment == \App\Topic::CAN_COMMENT_TRUE?\App\Topic::CAN_COMMENT_FALSE:\App\Topic::CAN_COMMENT_TRUE }}">{{ $topic->can_comment == \App\Topic::CAN_COMMENT_TRUE?"禁止回应":"允许回应" }}</a>
                                </li>
                                <li class="separator">|</li>
                                <li><a href="javascript:void(0);" title="" class="topic-admin-opt"
                                       url="/topic/ban/{{ $topic->status == \App\Topic::STATUS_NORMAL?\App\Topic::STATUS_BAN:\App\Topic::STATUS_NORMAL }}">{{ $topic->status == \App\Topic::STATUS_NORMAL?"封禁":"解封" }}</a>
                                </li>
                                @if($topic->creator->id != session('uid'))
                                    <li class="separator">|</li>
                                    <li><a href="javascript:void(0);" title="" class="topic-del">删除</a></li>
                                @else
                                    <li class="separator">|</li>
                                @endif
                            @endif
                            @if($topic->creator->id == session('uid'))
                                <li><a href="/topic/edit?tid={{ $topic->id }}" title="">修改</a></li>
                                <li class="separator">|</li>
                                <li><a href="javascript:void(0);" title="" class="topic-del">删除</a></li>
                            @endif
                        </ul>
                        <div class="other-opt">
                            <a href="javascript:void(0);" title=""
                               class="save{{ $topic->collects()->where('u_id', session('uid'))->get()->isEmpty()?"":" saved" }}"
                               tid="{{ $topic->id }}">{{ $topic->collects()->where('u_id', session('uid'))->get()->isEmpty()?"":"已" }}
                                收藏({{ $topic->collects()->count() }})</a>
                            <a href="javascript:void(0);" title=""
                               class="opt-like{{ $topic->likes()->where('u_id', session('uid'))->get()->isEmpty()?"":" liked" }}"
                               tid="{{ $topic->id }}"
                               type="0">{{ $topic->likes()->where('u_id', session('uid'))->get()->isEmpty()?"":"已" }}
                                赞({{ $topic->likes()->count() }})</a>
                        </div>
                    </div>
                </div>
                <div class="operation">
                    <ul>
                        <li><a href="?item=replies" title=""
                               class="{{ request('item', 'replies') == 'replies'?'active':'' }}">回应</a></li>
                        <li><a href="?item=likes" title=""
                               class="{{ request('item', 'replies') == 'likes'?'active':'' }}">赞</a></li>
                        <li><a href="?item=collects" title=""
                               class="{{ request('item', 'replies') == 'collects'?'active':'' }}">收藏</a></li>
                        @if(request('item', 'replies') == 'replies')
                            <li class="only-auther"><a href="?only={{request('only', 'false') == 'false'?'true':'false'}}" title="">只看楼主</a></li>
                        @endif
                    </ul>
                </div>
                <div class="opt-show">
                    @if(request('item', 'replies') == 'replies')
                        <div class="replies">
                            @if(request('page', '1') == 1 and !$topic->goodComments()->isEmpty() and request('only', 'false') == 'false')
                                <h4>最赞回应</h4>
                                <div class="good-replies">
                                    @foreach( $topic->goodComments() as $k=>$comment )
                                        <div class="reply">
                                            <img src="{{ $comment->creator->avatar }}" alt="" class="head-portrait">
                                            <div class="reply-right">
                                                <div class="author">
                                                    <a href="/member?uid={{ $comment->creator->id }}">{{ $comment->creator->username }}</a>
                                                </div>
                                                <div class="content">
                                                    <p>{{ $comment->content }}
                                                        @if( !empty($comment->image) )
                                                            <img src="{{ $comment->image }}" alt="">
                                                        @endif
                                                    </p>
                                                </div>
                                                <span class="time">发布于{{ $comment->created_at }}</span>
                                                <div class="opt">
                                                    <a href="javascript:void(0);" title="" class="report"
                                                       hidden="" data-type="3" data-target="{{$comment->id}}">举报</a>
                                                    @if($comment->u_id == session('uid') or $comment->topic->u_id == session('uid') or $comment->topic->group->leader->find(session('uid')) or $comment->topic->group->admin->find(session('uid')))
                                                        <a href="javascript:void(0);" title="" class="delete" hidden=""
                                                           cid="{{ $comment->id }}">删除</a>
                                                    @endif
                                                    <a href="javascript:void(0);" title="" class="opt-like" type="1"
                                                       tid="{{ $comment->id }}">{{ $comment->likes()->where('u_id', session('uid'))->get()->isEmpty()?"":"已" }}
                                                        赞({{ $comment->likes()->count() }})</a>
                                                </div>
                                                <ul class="comments">
                                                    @foreach( $comment->getComments() as $kk=>$com )
                                                        <li>
                                                            <p>
                                                                <a href="/member?uid={{ $com->creator->id }}">{{ $com->creator->username }}</a>
                                                                @if($com->parent_id != $comment->id)
                                                                    <span>回复</span>
                                                                    <a href="/member?uid={{ $com->parent->creator->id }}">{{ $com->parent->creator->username }}</a>
                                                                @endif
                                                                <span>:</span>{{ $com->content }}
                                                                @if( !empty($com->image) )
                                                                    <img src="{{ $com->image }}" alt="">
                                                                @endif</p>
                                                            <span class="time">发布于{{ $com->created_at }}</span>
                                                            <div class="opt">
                                                                <a href="javascript:void(0);" title="" class="report"
                                                                   hidden="" data-type="3" data-target="{{$com->id}}">举报</a>
                                                                @if($com->u_id == session('uid') or $com->topic->u_id == session('uid') or $com->topic->group->leader->find(session('uid')) or $com->topic->group->admin->find(session('uid')))
                                                                    <a href="javascript:void(0);" title=""
                                                                       class="delete" hidden=""
                                                                       cid="{{ $com->id }}">删除</a>
                                                                @endif
                                                                <a href="javascript:void(0);" title=""
                                                                   class="btn-reply">回应</a>
                                                                <a href="javascript:void(0);" title="" class="opt-like"
                                                                   type="1"
                                                                   tid="{{ $com->id }}">{{ $com->likes()->where('u_id', session('uid'))->get()->isEmpty()?"":"已" }}
                                                                    赞({{ $com->likes()->count() }})</a>
                                                            </div>
                                                            <form action="#" method="post" hidden=""
                                                                  class="add-comment">
                                                                {{ csrf_field() }}
                                                                <input type="hidden" name="type" value="1">
                                                                <input type="hidden" name="target"
                                                                       value="{{ $com->id }}">
                                                                <textarea rows="1" name="content"
                                                                          placeholder="回复{{ $com->creator->username }}"></textarea>
                                                                <a href="javascript:void(0);" class="upload-img"><img
                                                                            src="" alt="" hidden=""
                                                                            class="img-show"></a>
                                                                <a href="javascript:void(0);" hidden="" class="img-del">删除图片</a>
                                                                <input type="file" name="img" value="" placeholder=""
                                                                       hidden="" class="img-in">
                                                                <input type="button" name="" value="添加图片"
                                                                       class="add-img">
                                                                <input type="submit" name="" value="回复"
                                                                       class="send-comment">
                                                            </form>
                                                        </li>
                                                    @endforeach
                                                    <li>
                                                        <form action="#" method="post" class="last-form add-comment">
                                                            {{ csrf_field() }}
                                                            <input type="hidden" name="type" value="1">
                                                            <input type="hidden" name="target"
                                                                   value="{{ $comment->id }}">
                                                            <textarea rows="1" name="content"
                                                                      placeholder="回复{{ $comment->creator->username }}"></textarea>
                                                            <a href="javascript:void(0);" class="upload-img"><img src=""
                                                                                                                  alt=""
                                                                                                                  hidden=""
                                                                                                                  class="img-show"></a>
                                                            <a href="javascript:void(0);" hidden=""
                                                               class="img-del">删除图片</a>
                                                            <input type="file" name="img" value="" placeholder=""
                                                                   hidden="" class="img-in">
                                                            <input type="button" name="" value="添加图片" class="add-img">
                                                            <input type="submit" name="" value="回复"
                                                                   class="send-comment">
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if(request('only', 'false') == 'false')
                                @foreach( $topic->comments()->paginate($page) as $k=>$comment )
                                    <div class="reply">
                                        <img src="{{ $comment->creator->avatar }}" alt="" class="head-portrait">
                                        <div class="reply-right">
                                            <div class="author">
                                                <a href="/member?uid={{ $comment->creator->id }}">{{ $comment->creator->username }}</a>
                                                <span>{{ $loop->iteration+($page*(request('page', 1)-1)) }}#</span>
                                            </div>
                                            <div class="content">
                                                <p>{{ $comment->content }}
                                                    @if( !empty($comment->image) )
                                                        <img src="{{ $comment->image }}" alt="">
                                                    @endif
                                                </p>
                                            </div>
                                            <span class="time">发布于{{ $comment->created_at }}</span>
                                            <div class="opt">
                                                <a href="javascript:void(0);" title="" class="report" hidden=""  data-type="3" data-target="{{$comment->id}}">举报</a>
                                                @if($comment->u_id == session('uid') or $comment->topic->u_id == session('uid') or $comment->topic->group->leader->find(session('uid')) or $comment->topic->group->admin->find(session('uid')))
                                                    <a href="javascript:void(0);" title="" class="delete" hidden=""
                                                       cid="{{ $comment->id }}">删除</a>
                                                @endif
                                                <a href="javascript:void(0);" title="" class="opt-like" type="1"
                                                   tid="{{ $comment->id }}">{{ $comment->likes()->where('u_id', session('uid'))->get()->isEmpty()?"":"已" }}
                                                    赞({{ $comment->likes()->count() }})</a>
                                            </div>
                                            <ul class="comments">
                                                @foreach( $comment->getComments() as $kk=>$com )
                                                    <li>
                                                        <p>
                                                            <a href="/member?uid={{ $com->creator->id }}">{{ $com->creator->username }}</a>
                                                            @if($com->parent_id != $comment->id)
                                                                <span>回复</span>
                                                                <a href="/member?uid={{ $com->parent->creator->id }}">{{ $com->parent->creator->username }}</a>
                                                            @endif
                                                            <span>:</span>{{ $com->content }}
                                                            @if( !empty($com->image) )
                                                                <img src="{{ $com->image }}" alt="">
                                                            @endif</p>
                                                        <span class="time">发布于{{ $com->created_at }}</span>
                                                        <div class="opt">
                                                            <a href="javascript:void(0);" title="" class="report"
                                                               hidden="" data-type="3" data-target="{{$com->id}}">举报</a>
                                                            @if($com->u_id == session('uid') or $com->topic->u_id == session('uid') or $com->topic->group->leader->find(session('uid')) or $com->topic->group->admin->find(session('uid')))
                                                                <a href="javascript:void(0);" title="" class="delete"
                                                                   hidden="" cid="{{ $com->id }}">删除</a>
                                                            @endif
                                                            <a href="javascript:void(0);" title=""
                                                               class="btn-reply">回应</a>
                                                            <a href="javascript:void(0);" title="" class="opt-like"
                                                               type="1"
                                                               tid="{{ $com->id }}">{{ $com->likes()->where('u_id', session('uid'))->get()->isEmpty()?"":"已" }}
                                                                赞({{ $com->likes()->count() }})</a>
                                                        </div>
                                                        <form action="#" method="post" hidden="" class="add-comment">
                                                            {{ csrf_field() }}
                                                            <input type="hidden" name="type" value="1">
                                                            <input type="hidden" name="target" value="{{ $com->id }}">
                                                            <textarea rows="1" name="content"
                                                                      placeholder="回复{{ $com->creator->username }}"></textarea>
                                                            <a href="javascript:void(0);" class="upload-img"><img src=""
                                                                                                                  alt=""
                                                                                                                  hidden=""
                                                                                                                  class="img-show"></a>
                                                            <a href="javascript:void(0);" hidden=""
                                                               class="img-del">删除图片</a>
                                                            <input type="file" name="img" value="" placeholder=""
                                                                   hidden=""
                                                                   class="img-in">
                                                            <input type="button" name="" value="添加图片" class="add-img">
                                                            <input type="submit" name="" value="回复"
                                                                   class="send-comment">
                                                        </form>
                                                    </li>
                                                @endforeach
                                                <li>
                                                    <form action="#" method="post" class="last-form add-comment">
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="type" value="1">
                                                        <input type="hidden" name="target" value="{{ $comment->id }}">
                                                        <textarea rows="1" name="content"
                                                                  placeholder="回复{{ $comment->creator->username }}"></textarea>
                                                        <a href="javascript:void(0);" class="upload-img"><img src=""
                                                                                                              alt=""
                                                                                                              hidden=""
                                                                                                              class="img-show"></a>
                                                        <a href="javascript:void(0);" hidden="" class="img-del">删除图片</a>
                                                        <input type="file" name="img" value="" placeholder="" hidden=""
                                                               class="img-in">
                                                        <input type="button" name="" value="添加图片" class="add-img">
                                                        <input type="submit" name="" value="回复" class="send-comment">
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="page">
                                    {{ $topic->comments()->paginate($page)->links() }}
                                </div>
                            @else
                                @foreach( $topic->comments()->where('u_id', $topic->u_id)->get() as $k=>$comment )
                                    <div class="reply">
                                        <img src="{{ $comment->creator->avatar }}" alt="" class="head-portrait">
                                        <div class="reply-right">
                                            <div class="author">
                                                <a href="/member?uid={{ $comment->creator->id }}">{{ $comment->creator->username }}</a>
                                                <span>{{ $loop->iteration+($page*(request('page', 1)-1)) }}#</span>
                                            </div>
                                            <div class="content">
                                                <p>{{ $comment->content }}
                                                    @if( !empty($comment->image) )
                                                        <img src="{{ $comment->image }}" alt="">
                                                    @endif
                                                </p>
                                            </div>
                                            <span class="time">发布于{{ $comment->created_at }}</span>
                                            <div class="opt">
                                                <a href="javascript:void(0);" title="" class="report" hidden="" data-type="3" data-target="{{$comment->id}}">举报</a>
                                                @if($comment->u_id == session('uid') or $comment->topic->u_id == session('uid') or $comment->topic->group->leader->find(session('uid')) or $comment->topic->group->admin->find(session('uid')))
                                                    <a href="javascript:void(0);" title="" class="delete" hidden=""
                                                       cid="{{ $comment->id }}">删除</a>
                                                @endif
                                                <a href="javascript:void(0);" title="" class="opt-like" type="1"
                                                   tid="{{ $comment->id }}">{{ $comment->likes()->where('u_id', session('uid'))->get()->isEmpty()?"":"已" }} 赞({{ $comment->likes()->count() }})</a>
                                            </div>
                                            <ul class="comments">
                                                @foreach( $comment->getComments() as $kk=>$com )
                                                    <li>
                                                        <p>
                                                            <a href="/member?uid={{ $com->creator->id }}">{{ $com->creator->username }}</a>
                                                            @if($com->parent_id != $comment->id)
                                                                <span>回复</span>
                                                                <a href="/member?uid={{ $com->parent->creator->id }}">{{ $com->parent->creator->username }}</a>
                                                            @endif
                                                            <span>:</span>{{ $com->content }}
                                                            @if( !empty($com->image) )
                                                                <img src="{{ $com->image }}" alt="">
                                                            @endif</p>
                                                        <span class="time">发布于{{ $com->created_at }}</span>
                                                        <div class="opt">
                                                            <a href="javascript:void(0);" title="" class="report"
                                                               hidden="" data-type="3" data-target="{{$com->id}}">举报</a>
                                                            @if($com->u_id == session('uid') or $com->topic->u_id == session('uid') or $com->topic->group->leader->find(session('uid')) or $com->topic->group->admin->find(session('uid')))
                                                                <a href="javascript:void(0);" title="" class="delete"
                                                                   hidden="" cid="{{ $com->id }}">删除</a>
                                                            @endif
                                                            <a href="javascript:void(0);" title=""
                                                               class="btn-reply">回应</a>
                                                            <a href="javascript:void(0);" title="" class="opt-like"
                                                               type="1"
                                                               tid="{{ $com->id }}">{{ $com->likes()->where('u_id', session('uid'))->get()->isEmpty()?"":"已" }}
                                                                赞({{ $com->likes()->count() }})</a>
                                                        </div>
                                                        <form action="#" method="post" hidden="" class="add-comment">
                                                            {{ csrf_field() }}
                                                            <input type="hidden" name="type" value="1">
                                                            <input type="hidden" name="target" value="{{ $com->id }}">
                                                            <textarea rows="1" name="content"
                                                                      placeholder="回复{{ $com->creator->username }}"></textarea>
                                                            <a href="javascript:void(0);" class="upload-img"><img src=""
                                                                                                                  alt=""
                                                                                                                  hidden=""
                                                                                                                  class="img-show"></a>
                                                            <a href="javascript:void(0);" hidden=""
                                                               class="img-del">删除图片</a>
                                                            <input type="file" name="img" value="" placeholder=""
                                                                   hidden=""
                                                                   class="img-in">
                                                            <input type="button" name="" value="添加图片" class="add-img">
                                                            <input type="submit" name="" value="回复"
                                                                   class="send-comment">
                                                        </form>
                                                    </li>
                                                @endforeach
                                                <li>
                                                    <form action="#" method="post" class="last-form add-comment">
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="type" value="1">
                                                        <input type="hidden" name="target" value="{{ $comment->id }}">
                                                        <textarea rows="1" name="content"
                                                                  placeholder="回复{{ $comment->creator->username }}"></textarea>
                                                        <a href="javascript:void(0);" class="upload-img"><img src=""
                                                                                                              alt=""
                                                                                                              hidden=""
                                                                                                              class="img-show"></a>
                                                        <a href="javascript:void(0);" hidden="" class="img-del">删除图片</a>
                                                        <input type="file" name="img" value="" placeholder="" hidden=""
                                                               class="img-in">
                                                        <input type="button" name="" value="添加图片" class="add-img">
                                                        <input type="submit" name="" value="回复" class="send-comment">
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if($topic->group->allMember()->find(session('uid')))
                                <form action="#" method="post" class="add-comment">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="type" value="0">
                                    <input type="hidden" name="target" value="{{ $topic->id }}">
                                    <textarea name="content" rows="3"></textarea>
                                    <div>
                                        <a href="javascript:void(0);" class="upload-img"><img src="" alt="" hidden=""
                                                                                              class="img-show"></a>
                                        <a href="javascript:void(0);" hidden="" class="img-del">删除图片</a>
                                        <input type="file" name="img" value="" placeholder="" hidden="" class="img-in">
                                        <input type="button" name="" value="添加图片" class="add-img">
                                    </div>
                                    <input type="submit" name="" value="回复" class="send-comment">
                                </form>
                            @else
                                <div class="tips">
                                    <span>只有小组成员才能发言</span>
                                </div>
                            @endif
                        </div>
                    @elseif(request('item', 'replies') == 'likes')
                        <div class="likes">
                            @foreach( $topic->likes as $k=>$like )
                                <div class="like">
                                    <img src="{{ $like->creator->avatar }}" alt="">
                                    <a href="/member?uid={{ $like->creator->id }}"
                                       title="">{{ $like->creator->username }}</a>
                                    <span>赞了这篇讨论</span>
                                    <span class="time">{{ $like->created_at }}</span>
                                </div>
                            @endforeach

                        </div>
                    @elseif(request('item', 'replies') == 'collects')
                        <div class="collects">
                            @foreach( $topic->collects as $k=>$collect )
                                <div class="collect">
                                    <img src="{{ $collect->creator->avatar }}" alt="">
                                    <a href="/member?uid={{ $collect->creator->id }}"
                                       title="">{{ $collect->creator->username }}</a>
                                    <span>收藏到收藏夹</span>
                                    <span class="time">{{ $collect->created_at }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="main-right">
                <div class="group">
                    @if($topic->group->allMember->find(session('uid')))
                        <div class="group-info-in">
                            <div class="group-top">
                                <img src="{{ $topic->group->icon }}" alt="">
                                <div>
                                    <a href="/group/{{ $topic->group->id }}" title="">{{ $topic->group->name }}</a>
                                    <p>
                                        @if($topic->group->leader->find(session('uid')))
                                            我是小组的组长
                                        @elseif($topic->group->admin->find(session('uid')))
                                            我是小组的管理员
                                        @else
                                            我是小组的成员
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="group-info-notin">
                            <div class="group-top">
                                <img src="{{ $topic->group->icon }}" alt="">
                                <div>
                                    <a href="/group/{{ $topic->group->id }}" title="">{{ $topic->group->name }}</a>
                                </div>
                            </div>
                            <div class="group-bottom">
                                <p><span>{{ $topic->group->allMember->count() }}</span>人聚集在这个小组</p>
                                <a href="javascript:void(0);" title="">申请加入小组</a>
                            </div>
                        </div>
                    @endif
                    <h3>最新讨论 <span>(<a href="#" title="">更多</a> )</span></h3>
                    <ul>
                        @foreach( $topic->group->topics()->orderBy('created_at', 'desc')->take(5)->get() as $k=>$t)
                            <li><a href="/topic/{{ $t->id }}" title="">{{ $t->title }}</a><span>({{ $t->creator->username }})</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop