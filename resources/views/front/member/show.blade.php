@extends('front.layout')
@section('title')用户@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/member.css') }}">
    <link rel="stylesheet" href="{{ asset('js/cropperjs/cropper.min.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/member.js') }}"></script>
    <script type="application/javascript" src="{{ asset('js/cropperjs/cropper.min.js') }}"></script>
    <script type="application/javascript" src="{{ asset('js/autosize.min.js')}}"></script>
    <script type="application/javascript" src="{{ asset('js/get_url_param.js') }}"></script>
@stop
@section('main')
    <div class="main">
        <div class="wrapper">
            <div class="main-left">
                <div class="member-info">
                    <img src="{{ $user->avatar }}" alt="" class="head_portrait_img">
                    <div>
                        <h3>{{ $user->username }}</h3>
                        <form action="#" method="get" accept-charset="utf-8" hidden="" class="signature-form">
                            <input type="text" name="signature" value="" placeholder="" class="set-in" size="30"
                                   maxlength="30"><input type="submit" name="" value="修改" class="set-go"><input
                                    type="button" name="" value="取消" class="set-cancer">
                        </form>
                        <p>
                            <span class="signature-content">{{ $user->signature }}</span>
                            @if( session('uid')==$user->id )
                                <a href="javascript:void(0);" title=""
                                   class="set-signature" {{ empty($user->signature)?'hidden=""':'' }}>(编辑)</a>
                                <a href="javascript:void(0);" title=""
                                   class="add-signature" {{ empty($user->signature)?'':'hidden=""' }}>(添加签名档)</a>
                            @endif
                        </p>
                    </div>
                </div>
                <ul class="opt">
                    <li><a href="javascript:void(0);" title="" tab="groups" class="{{ $opt=='groups'?'active':'' }}">加入的小组</a></li>
                    <li><a href="javascript:void(0);" title="" tab="issue" class="{{ $opt=='issue'?'active':'' }}">发布</a></li>
                    <li><a href="javascript:void(0);" title="" tab="respond" class="{{ $opt=='respond'?'active':'' }}">回应</a></li>
                    <li><a href="javascript:void(0);" title="" tab="collect" class="{{ $opt=='collect'?'active':'' }}">收藏夹</a></li>
                    <li><a href="javascript:void(0);" title="" tab="idol" class="{{ $opt=='idol'?'active':'' }}">关注了</a></li>
                    <li><a href="javascript:void(0);" title="" tab="fans" class="{{ $opt=='fans'?'active':'' }}">关注者</a></li>
                    @if( session('uid')==$user->id )
                        <li><a href="javascript:void(0);" title="" tab="blacklist" class="{{ $opt=='blacklist'?'active':'' }}">黑名单</a></li>
                        <li><a href="javascript:void(0);" title="" tab="setting" class="{{ $opt=='setting'?'active':'' }}">设置</a></li>
                    @endif
                </ul>
                @if( $opt == 'groups' )
                    <div class="groups">
                        @if( session('uid')==$user->id )
                            @if($user->joinGroups()->wherePivotIn('role', array(\App\GroupMember::ROLE_LEADER, \App\GroupMember::ROLE_ADMIN))->count() != 0)
                            <h3>管理的小组</h3>
                                <ul class="group-list">
                                    @foreach( $user->joinGroups()->wherePivotIn('role', array(\App\GroupMember::ROLE_LEADER, \App\GroupMember::ROLE_ADMIN))->get() as $k=>$group)
                                        <li>
                                            <a href="/group/{{$group->id}}" title="" class="icon"><img src="{{$group->icon}}" alt=""></a>
                                            <div>
                                                <a href="/group/{{$group->id}}" title="" class="name">{{$group->name}}</a>
                                                <span>({{$group->allMember()->count()}})</span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                                @if($user->joinGroups()->wherePivot('role', \App\GroupMember::ROLE_MEMBER)->count() != 0)
                                    <h3>加入的小组</h3>
                                    <ul class="group-list">
                                        @foreach( $user->joinGroups()->wherePivot('role', \App\GroupMember::ROLE_MEMBER)->get() as $k=>$group)
                                            <li>
                                                <a href="/group/{{$group->id}}" title="" class="icon"><img src="{{$group->icon}}" alt=""></a>
                                                <div>
                                                    <a href="/group/{{$group->id}}" title="" class="name">{{$group->name}}</a>
                                                    <span>({{$group->allMember()->count()}})</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                        @else
                            <ul class="group-list">
                                @foreach( $user->joinGroups as $k=>$group)
                                    <li>
                                        <a href="/group/{{$group->id}}" title="" class="icon"><img src="{{$group->icon}}" alt=""></a>
                                        <div>
                                            <a href="/group/{{$group->id}}" title="" class="name">{{$group->name}}</a>
                                            <span>({{$group->allMember()->count()}})</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @elseif( $opt == 'issue' )
                    <div class="topic-list">
                        @foreach($user->topicsAsSender()->paginate(1) as $k=>$topic)
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
                            {{ $user->topicsAsSender()->paginate(1)->appends(['uid' => $user->id])->links() }}
                        </div>
                    </div>
                @elseif($opt == 'respond' )
                    <div class="topic-list">
                        @foreach($user->topicsAsCommenter()->paginate(1) as $k=>$topic)
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
                                            <p>{{ str_limit(strip_tags($topic->pivot->content), 200) }}</p>
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
                            {{ $user->topicsAsCommenter()->paginate(1)->appends(['uid' => $user->id])->links() }}
                        </div>
                    </div>
                @elseif($opt == 'collect' )
                    <div class="topic-list">
                        @foreach($user->topicsAsCollector()->paginate(1) as $k=>$topic)
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
                            {{ $user->topicsAsCollector()->paginate(1)->appends(['uid' => $user->id])->links() }}
                        </div>
                    </div>
                @elseif($opt == 'idol' )
                    <div class="member-list">
                        <ul class="list">
                            @foreach( $user->idol()->paginate(1) as $k=>$idol )
                                <li><a href="/member?uid={{ $idol->id }}" title=""><img src="{{ $idol->avatar }}" alt=""></a>
                                    <a href="/member?uid={{ $idol->id }}" title="" class="username">{{ $idol->username }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="page">
                        {{ $user->idol()->paginate(1)->appends(['uid' => $user->id])->links() }}
                    </div>
                @elseif($opt == 'fans' )
                    <div class="member-list">
                        <ul class="list">
                            @foreach( $user->fans()->paginate(1) as $k=>$fans )
                                <li><a href="/member?uid={{ $fans->id }}" title=""><img src="{{ $fans->avatar }}" alt=""></a>
                                    <a href="/member?uid={{ $fans->id }}" title="" class="username">{{ $fans->username }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="page">
                        {{ $user->fans()->paginate(1)->appends(['uid' => $user->id])->links() }}
                    </div>
                @elseif($opt == 'blacklist' )
                    <div class="member-list">
                        <ul class="list">
                            @foreach( $user->blacklist()->paginate(1) as $k=>$target )
                                <li><a href="/member?uid={{ $target->id }}" title=""><img src="{{ $target->avatar }}" alt=""></a>
                                    <a href="/member?uid={{ $target->id }}" title="" class="username">{{ $target->username }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="page">
                        {{ $user->blacklist()->paginate(1)->appends(['uid' => $user->id])->links() }}
                    </div>
                @elseif($opt == 'setting' )
                    @if( session('uid')==$user->id )
                        <div class="base-info">
                            <form action="#" method="post" accept-charset="utf-8" enctype="multipart/form-data"
                                  id="base-info">
                                {{csrf_field()}}
                                <table>
                                    <tr>
                                        <th>头像</th>
                                        <td><img src="{{ $user->avatar }}" alt="" class="head_portrait_img"><input
                                                    type="file"
                                                    name="head_portrait"
                                                    hidden=""
                                                    class="head_portrait"><a
                                                    href="javascript:void(0);" title=""
                                                    onclick="$('.head_portrait').click();">更换头像</a>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th>昵称</th>
                                        <td><input type="text" name="username" value="{{ $user->username }}" placeholder=""
                                                   class="username"></td>
                                        <td class="error-tip" hidden="">错误提示</td>
                                    </tr>
                                    <tr>
                                        <th>性别</th>
                                        <td class="sex">
                                            <input type="radio" name="sex" value="1"
                                                   {{ $user->sex==1?'checked':'' }} class="sex"><label>男</label>
                                            <input type="radio" name="sex" value="2"
                                                   {{ $user->sex==2?'checked':'' }} class="sex"><label>女</label>
                                            <input type="radio" name="sex" value="0"
                                                   {{ $user->sex==0?'checked':'' }} class="sex"><label>保密</label>
                                        </td>
                                        <td class="error-tip" hidden="">错误提示</td>
                                    </tr>
                                    <tr>
                                        <th>年龄</th>
                                        <td><input type="text" name="age" value="{{ $user->age }}" placeholder=""
                                                   class="age">
                                        </td>
                                        <td class="error-tip" hidden="">错误提示</td>
                                    </tr>
                                    <tr>
                                        <th>长居地</th>
                                        <td><input type="text" name="place" value="{{ $user->place }}" placeholder=""
                                                   class="place" readonly="readonly "></td>
                                        <td class="error-tip" hidden="">错误提示</td>
                                    </tr>
                                    <tr>
                                        <th>密码</th>
                                        <td><a href="/update_password" style="margin-left: 0">修改密码</a></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th>邮箱</th>
                                        <td>
                                            <input type="text" name="email" value="{{ $user->email }}" placeholder=""
                                                   class="email-in email" hidden="">
                                            <span class="email-show">{{ $user->email }}</span>
                                            <a href="javascript:void(0);" title=""
                                               {{ empty($user->confirmation)?"hidden":"" }} class="send-email">激活</a>
                                            <a href="javascript:void(0);" title="" class="set-email">更改</a>
                                        </td>
                                        <td class="error-tip" hidden="">错误提示</td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <td><input type="submit" name="" value="更新设置" class="set-info-go"></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    @endif
                @endif
            </div>
            <div class="main-right">
                <div class="main-right-wrapper">
                    <div class="member">
                        <img src="{{ $user->avatar }}" alt="" class="head_portrait_img">
                        <div class="other-info">
                            <div>
                                长居： {{ empty($user->place)?'保密':$user->place }}
                            </div>
                            <div>
                                {{ $user->created_at->format('Y-m-d') }}加入
                            </div>
                        </div>
                    </div>
                    @if( session('uid')!=$user->id )
                        <ul>
                            @if($user->beBlacklist->find(session('uid')))
                                <li>
                                        <a href="javascript:void(0);" title="" class="add-idol" style="display: none;">关注此人+</a>
                                        <a href="javascript:void(0);" title="" class="del-idol" style="display: none;">已关注√</a>
                                </li>
                                <li><a href="/chat/{{ $user->id }}" title="" class="chat" style="display: none;">私信</a></li>
                                <li>
                                    <a href="javascript:void(0);" title="" class="add-blacklist" style="display: none;">加入黑名单</a>
                                    <a href="javascript:void(0);" title="" class="del-blacklist">已加入黑名单</a></li>
                            @else
                                <li>
                                    @if($user->fans->find(session('uid')))
                                        <a href="javascript:void(0);" title="" class="add-idol" style="display: none;">关注此人+</a>
                                        <a href="javascript:void(0);" title="" class="del-idol">已关注√</a>
                                    @else
                                        <a href="javascript:void(0);" title="" class="add-idol">关注此人+</a>
                                        <a href="javascript:void(0);" title="" class="del-idol" style="display: none;">已关注√</a>
                                    @endif
                                </li>
                                <li><a href="/chat/{{ $user->id }}" title="" class="chat">私信</a></li>
                                <li><a href="javascript:void(0);" title="" class="add-blacklist">加入黑名单</a>
                                    <a href="javascript:void(0);" title="" class="del-blacklist" style="display: none;">已加入黑名单</a>
                                </li>
                            @endif
                        </ul>
                    @endif
                    <hr/>
                    @if( session('uid')==$user->id )
                        <form action="#" method="get" accept-charset="utf-8" class="introduce-form" hidden="">
                            <textarea name="introduce" class="set-introduce-in" maxlength="6000"></textarea><input
                                    type="submit" name="" value="修改" class="set-introduce-go"><input type="button"
                                                                                                     name=""
                                                                                                     value="取消"
                                                                                                     class="set-introduce-cancer">
                        </form>
                        <a href="javascript:void(0);" title=""
                           class="add-introduce" {{ empty($user->introduce)?'':'hidden=""' }}>(添加介绍)</a>
                        <p class="introduce-content">{{ $user->introduce }}</p>
                        <a href="javascript:void(0);"
                           class="set-introduce" {{ empty($user->introduce)?'hidden=""':'' }}>(编辑)</a>
                    @else
                        <p class="introduce-content">{{ $user->introduce }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop