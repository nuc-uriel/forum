@extends('front.layout')
@section('title')小组管理@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('css/edit_group.css') }}">
    <link rel="stylesheet" href="{{ asset('js/cropperjs/cropper.min.css') }}">
@stop
@section('js')
    <script type="application/javascript" src="{{ asset('js/edit_group.js') }}"></script>
    <script type="application/javascript" src="{{ asset('js/highcharts.js') }}"></script>
    <script type="application/javascript" src="{{ asset('js/series-label.js') }}"></script>
    <script type="application/javascript" src="{{ asset('js/cropperjs/cropper.min.js') }}"></script>
    <script type="application/javascript" src="{{ asset('js/get_url_param.js') }}"></script>
@stop
@section('main')
    <div class="page-info">
        <div class="wrapper">
            <span>
                @if( $opt=='base-info' )
                    {{ $group->name }}小组基本设置
                @elseif( $opt=='member-edit' )
                    {{ $group->name }}小组成员管理
                @elseif( $opt=='data-statistics' )
                    {{ $group->name }}小组数据统计
                @elseif( $opt=='ban-manage' )
                    {{ $group->name }}小组违禁讨论
                @elseif( $opt=='other-set' )
                    {{ $group->name }}小组高级设定
                @elseif( $opt=='log' )
                    {{ $group->name }}小组管理日志
                @endif
            </span>
        </div>
    </div>
    <div class="main">
        <div class="wrapper">
            <div class="main-left">
                <ul class="tabs">
                    <li><a href="javascript:void(0);" tab="base-info" title=""
                           class="{{ $opt=='base-info'?'active':'' }}">基本信息</a></li>
                    <li><a href="javascript:void(0);" tab="member-edit" title=""
                           class="{{ $opt=='member-edit'?'active':'' }}">成员管理</a></li>
                    <li><a href="javascript:void(0);" tab="data-statistics" title=""
                           class="{{ $opt=='data-statistics'?'active':'' }}">数据统计</a></li>
                    <li><a href="javascript:void(0);" tab="ban-manage" title=""
                           class="{{ $opt=='ban-manage'?'active':'' }}">违禁讨论</a></li>
                    <li><a href="javascript:void(0);" tab="other-set" title=""
                           class="{{ $opt=='other-set'?'active':'' }}">高级设定</a></li>
                    <li><a href="javascript:void(0);" tab="log" title="" class="{{ $opt=='log'?'active':'' }}">管理日志</a>
                    </li>
                </ul>
                @if( $opt=='base-info' )
                    <div class="base-info">
                        <form action="#" method="post" accept-charset="utf-8" id="base-info-form">
                            {{ csrf_field() }}
                            <table>
                                <tr>
                                    <th>小组名称</th>
                                    <td colspan="2"><input type="text" name="name" class="name"
                                                           value="{{ $group->name }}"
                                                           placeholder=""></td>
                                    <td class="error-tip" hidden=""></td>
                                </tr>
                                <tr>
                                    <th>小组介绍</th>
                                    <td colspan="2"><textarea name="introduce"
                                                              class="introduce">{{ $group->introduce }}</textarea></td>
                                    <td class="error-tip" hidden=""></td>
                                </tr>
                                <tr>
                                    <th>小组图标</th>
                                    <td colspan="2"><input type="hidden" name="path" class="path"
                                                           value="{{ $group->icon }}">
                                        <input type="file" hidden="" class="icon" name="icon">
                                        <img src="{{ $group->icon }}" alt="" class="icon_show">
                                        <a href="javascript:void(0);" title="" onclick="$('.icon').click();"
                                           class="update_icon">更新</a></td>
                                    <td class="error-tip" hidden=""></td>
                                </tr>
                                <tr>
                                    <th>标签</th>
                                    <td colspan="2"><input type="text" name="group_label" class="group_label"
                                                           value="{{ $group->labels->implode('name', ' ') }}"
                                                           placeholder="">
                                    </td>
                                    <td class="error-tip" hidden=""></td>
                                </tr>
                                <tr>
                                    <th></th>
                                    <td colspan="3">
                                        <div>
                                            标签作为关键词可以被用户搜索到(最多5个标签，多个标签之间用空格隔开)
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>成员名称</th>
                                    <td><label>小组管理员</label></td>
                                    <td><label>小组成员</label></td>
                                    <td></td>
                                </tr>
                                <tr class="member-name">
                                    <th></th>
                                    <td><input type="text" name="admin_as" class="admin_as"
                                               value="{{ $group->admin_as }}"
                                               placeholder=""></td>
                                    <td><input type="text" name="member_as" class="member_as"
                                               value="{{ $group->member_as }}" placeholder=""></td>
                                    <td class="error-tip" hidden=""></td>
                                </tr>
                                <tr>
                                    <th></th>
                                    <td><input type="submit" name="" value="保存" class="set-info-go"></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                @elseif($opt=='member-edit')
                    <div class="member-edit">
                        @if( !empty(request('name', '')) )
                            <div class="member_list">
                                <h4 class="role">搜索结果：{{ request('name') }}</h4>
                                <ul class="list">
                                    @foreach($group->allMember()->where('username', 'like', '%'.request('name').'%')->get() as $k=>$member)
                                        <li uid="{{ $member->id }}" class="member-item">
                                            <div class="member-opt">
                                                <a href="/member?uid={{ $member->id }}" title="">
                                                    <img src="{{ $member->avatar }}" alt=""></a>
                                                <ul>
                                                    @if($group->allMember->find(session('uid'))->pivot->role == \App\GroupMember::ROLE_LEADER)
                                                        @if($member->pivot->role == \App\GroupMember::ROLE_ADMIN)
                                                            <li><a href="javascript:void(0);" title="转让小组组长给{{ $member->username }}" class="set-leader">^</a></li>
                                                            <li><a href="javascript:void(0);" title="把{{ $member->username }}降为{{ empty($group->member_as)?$group->member_as:'成员' }}" class="revocation-admin">v</a></li>
                                                        @elseif($member->pivot->role == \App\GroupMember::ROLE_MEMBER)
                                                            <li><a href="javascript:void(0);" title="把{{ $member->username }}升为{{ empty($group->admin_as)?$group->admin_as:'管理员' }}" class="appoint-admin">^</a></li>
                                                            <li><a href="javascript:void(0);" title="把{{ $member->username }}踢出小组" class="del-member">k</a></li>
                                                            <li><a href="javascript:void(0);" title="把{{ $member->username }}永久封禁" class="add-blacklist">x</a></li>
                                                        @endif
                                                    @elseif($group->allMember->find(session('uid'))->pivot->role == \App\GroupMember::ROLE_ADMIN)
                                                        @if($member->pivot->role == \App\GroupMember::ROLE_MEMBER)
                                                            <li><a href="javascript:void(0);" title="把{{ $member->username }}踢出小组" class="del-member">k</a></li>
                                                            <li><a href="javascript:void(0);" title="把{{ $member->username }}永久封禁" class="add-blacklist">x</a></li>
                                                        @endif
                                                    @endif
                                                </ul>
                                            </div>
                                            <a href="/member?uid={{ $member->id }}" title="" class="username">{{ $member->username }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="member_list">
                                <h4 class="role">组长</h4>
                                <ul class="list">
                                    @foreach($group->leader as $k=>$member)
                                        <li uid="{{ $member->id }}" class="member-item"><a
                                                    href="/member?uid={{ $member->id }}" title=""><img
                                                        src="{{ $member->avatar }}" alt=""></a>
                                            <a href="/member?uid={{ $member->id }}" title=""
                                               class="username">{{ $member->username }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            @if(!$group->admin->isEmpty())
                                <div class="member_list">
                                    <h4 class="role">{{ empty($group->admin_as)?$group->admin_as:'管理员' }}</h4>
                                    <ul class="list">
                                        @foreach($group->admin as $k=>$member)
                                            <li uid="{{ $member->id }}" class="member-item">
                                                <div class="member-opt"><a href="/member?uid={{ $member->id }}"
                                                                           title=""><img
                                                                src="{{ $member->avatar }}" alt=""></a>
                                                    <ul>
                                                        @if($group->allMember->find(session('uid'))->pivot->role == \App\GroupMember::ROLE_LEADER)
                                                            <li><a href="javascript:void(0);" title="转让小组组长给{{ $member->username }}" class="set-leader">^</a></li>
                                                            <li><a href="javascript:void(0);" title="把{{ $member->username }}降为{{ empty($group->member_as)?$group->member_as:'成员' }}" class="revocation-admin">v</a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                                <a href="/member?uid={{ $member->id }}" title=""
                                                   class="username">{{ $member->username }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if(!$group->member->isEmpty())
                                <div class="member_list">
                                    <h4 class="role">{{ empty($group->member_as)?$group->member_as:'成员' }}</h4>
                                    <ul class="list">
                                        @foreach($group->member as $k=>$member)
                                            <li uid="{{ $member->id }}" class="member-item">
                                                <div class="member-opt"><a href="/member?uid={{ $member->id }}"
                                                                           title=""><img
                                                                src="{{ $member->avatar }}" alt=""></a>
                                                    <ul>
                                                        <li>
                                                            @if($group->allMember->find(session('uid'))->pivot->role == \App\GroupMember::ROLE_LEADER)
                                                                <a href="javascript:void(0);"
                                                                   title="把{{ $member->username }}升为{{ empty($group->admin_as)?$group->admin_as:'管理员' }}"
                                                                   class="appoint-admin">^</a>
                                                            @endif
                                                        </li>
                                                        <li><a href="javascript:void(0);"
                                                               title="把{{ $member->username }}踢出小组" class="del-member">k</a>
                                                        </li>
                                                        <li><a href="javascript:void(0);"
                                                               title="把{{ $member->username }}永久封禁"
                                                               class="add-blacklist">x</a></li>
                                                    </ul>
                                                </div>
                                                <a href="/member?uid={{ $member->id }}" title=""
                                                   class="username">{{ $member->username }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if(!$group->waitConfirm->isEmpty())
                                <div class="member_list">
                                    <h4 class="role">申请加入小组</h4>
                                    <ul class="list">
                                        @foreach($group->waitConfirm as $k=>$member)
                                            <li uid="{{ $member->id }}" class="member-item">
                                                <div class="member-opt"><a href="/member?uid={{ $member->id }}"
                                                                           title=""><img
                                                                src="{{ $member->avatar }}" alt=""></a>
                                                    <ul>
                                                        <li><a href="javascript:void(0);"
                                                               title="通过{{ $member->username }}的申请" class="pass-apply">√</a>
                                                        </li>
                                                        <li><a href="javascript:void(0);"
                                                               title="拒绝{{ $member->username }}的申请"
                                                               class="refuse-apply">×</a></li>
                                                    </ul>
                                                </div>
                                                <a href="/member?uid={{ $member->id }}" title=""
                                                   class="username">{{ $member->username }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if(!$group->blacklist->isEmpty())
                                <div class="member_list">
                                    <h4 class="role">黑名单</h4>
                                    <ul class="list">
                                        @foreach($group->blacklist as $k=>$member)
                                            <li uid="{{ $member->id }}" class="member-item">
                                                <div class="member-opt"><a href="/member?uid={{ $member->id }}"
                                                                           title=""><img
                                                                src="{{ $member->avatar }}" alt=""></a>
                                                    <ul>
                                                        <li><a href="javascript:void(0);"
                                                               title="把{{ $member->username }}移出黑名单"
                                                               class="del-blacklist">×</a></li>
                                                    </ul>
                                                </div>
                                                <a href="/member?uid={{ $member->id }}" title=""
                                                   class="username">{{ $member->username }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endif

                    </div>
                @elseif($opt=='data-statistics')
                    <div class="data-statistics">
                        <ul class="opt">
                            <li><a href="javascript:void(0);" title="" class="set-range active" range="week">最近一周</a></li>
                            <li><a href="javascript:void(0);" title="" class="set-range" range="month">最近一月</a></li>
                        </ul>
                        <ul class="data-types">
                            <li class="chart-topic active"><a href=javascript:void(0); title="" class="set-chart" chart="topic">
                                    <div class="active">
                                        <p>新增讨论数</p>
                                        <p class="sum">1</p>
                                    </div>
                                </a></li>
                            <li class="chart-comment"><a href="javascript:void(0);" title="" class="set-chart" chart="comment">
                                    <div class="">
                                        <p>新增回应数</p>
                                        <p class="sum">1</p>
                                    </div>
                                </a></li>
                            <li class="chart-in"><a href="javascript:void(0);" title="" class="set-chart" chart="in">
                                    <div>
                                        <p>加入成员数</p>
                                        <p class="sum">1</p>
                                    </div>
                                </a></li>
                            <li class="chart-out"><a href="javascript:void(0);" title="" class="set-chart" chart="out">
                                    <div>
                                        <p>退出成员数</p>
                                        <p class="sum">1</p>
                                    </div>
                                </a></li>
                        </ul>
                        <div id="line-chart"></div>
                    </div>
                @elseif($opt=='ban-manage')
                    <div class="ban-manage">
                        <h4>违禁词管理</h4>
                        <form id="ban-form" action="" method="get" accept-charset="utf-8">
                            <input type="text" name="word" value="" placeholder="添加违禁词" class="add-in">
                            <input type="submit" name="" value="确定" class="add-go">
                            <span></span>
                        </form>
                        @if(!$group->banWords->isEmpty())
                            <table>
                                <thead>
                                <tr>
                                    <th>违禁词</th>
                                    <th>添加者</th>
                                    <th>添加时间</th>
                                    <th>删除</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach( $group->banWords as $k=>$word )
                                    <tr bid="{{ $word->id }}">
                                        <td>{{ $word->word }}</td>
                                        <td><a href="/member/{{ $word->creator->id }}"
                                               title="">{{ $word->creator->username }}</a></td>
                                        <td>{{ $word->created_at }}</td>
                                        <td><a href="javascript:void(0);" title="删除违禁词{{ $word->word }}"
                                               class="del-ban-word">×</a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                        @if( !$group->topics->where('can_comment', 1)->isEmpty() )
                        <h4>禁止回应的讨论</h4>
                        <table>
                            <thead>
                            <tr>
                                <th width="70%">讨论</th>
                                <th>作者</th>
                                <th>回应</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach( $group->topics->where('can_comment', 1) as $k=>$topic )
                            <tr>
                                <td><a href="/topic/{{ $topic->id }}" class="title">{{ $topic->title }}</a></td>
                                <td><a href="/member?uid={{ $topic->creator->id }}" title="">{{ $topic->creator->username }}</a></td>
                                <td>{{ $topic->comments() ->count() }}</td>
                                <td><a href="javascript:void(0);" title="" url="/topic/can_comment/0?tid={{ $topic->id }}" class="ban-opt">&gt;允许回应</a></td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                            @endif
                        @if( !$group->banTopics->isEmpty() )
                            <h4>封禁的讨论</h4>
                            <table>
                                <thead>
                                <tr>
                                    <th>讨论</th>
                                    <th>作者</th>
                                    <th>回应</th>
                                    <th colspan="2">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach( $group->banTopics as $k=>$topic )
                                    <tr>
                                        <td><a href="/topic/{{ $topic->id }}" class="title">{{ $topic->title }}</a></td>
                                        <td><a href="/member?uid={{ $topic->creator->id }}" title="">{{ $topic->creator->username }}</a></td>
                                        <td>{{ $topic->comments() ->count() }}</td>
                                        <td><a href="javascript:void(0);" title="" url="/topic/ban/0?tid={{ $topic->id }}" class="ban-opt">&gt;解封</a></td>
                                        <td><a href="javascript:void(0);" title="" url="/topic/del/0?tid={{ $topic->id }}" class="ban-opt">&gt;删除</a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                @elseif($opt=='other-set')
                    <div class="other-set">
                        <table>
                            <tr>
                                <th>友情小组</th>
                                <td>
                                    <ul>
                                        @foreach( $group->friendship() as $k=>$v )
                                            <li fid="{{ $v->pivot->id }}">
                                                <div>
                                                    <div><img src="{{ $v->icon }}" alt="">
                                                        <div>
                                                            @if($group->allMember->find(session('uid'))->pivot->role == \App\GroupMember::ROLE_LEADER)
                                                                <a href="javascript:void(0);" title=""
                                                                   class="del-friendship">x</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <a href="/group/{{ $v->id }}">{{ $v->name }}</a></div>
                                            </li>
                                        @endforeach
                                        @if($group->allMember->find(session('uid'))->pivot->role == \App\GroupMember::ROLE_LEADER)
                                            <?php $empty_group = 4 - $group->friendship()->count() ?>
                                            @while($empty_group--)
                                                <li><a href="javascript:void(0);" title=""
                                                       onclick="$('.add-friendship').slideToggle()"
                                                       class="add-friendship-but">+</a></li>
                                            @endwhile
                                        @endif
                                    </ul>
                                    <div class="add-friendship" hidden="">
                                        <form action="#" method="get" accept-charset="utf-8">
                                            <input type="text" name="name" value="" placeholder=""
                                                   class="add-friendship-in"
                                                   autocomplete="off">
                                            <input type="submit" name="" value="确认" class="add-friendsh-go">
                                            <input type="button" name="" value="取消" class="add-friendship-cancel"
                                                   onclick="$('.add-friendship').slideUp()">
                                        </form>
                                        <div class="tip">
                                            请输入友情小组名称
                                        </div>
                                        <ul class="associational-groups">
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>成员加入方式</th>
                                <td style="width:400px">
                            <span class="join_way_show">
                                @if($group->join_way==0)
                                    任何人都能加入
                                @elseif($group->join_way==1)
                                    需要申请，组长/管理员批准后才能加入
                                @elseif($group->join_way==2)
                                    不允许任何人加入
                                @endif
                            </span>
                                    <a href="javascript:void(0);" title=""
                                       onclick="$('.member-add-mode').slideToggle()">修改</a>
                                    <div class="member-add-mode" hidden="">
                                        <form action="#" method="get" accept-charset="utf-8">
                                            <div class="tip">
                                                修改成员加入方式
                                            </div>
                                            <ul>
                                                <li><input type="radio" name="join_way"
                                                           value="0" {{ $group->join_way==0?'checked':'' }}><span>任何人都能加入</span>
                                                </li>
                                                <li><input type="radio" name="join_way"
                                                           value="1" {{ $group->join_way==1?'checked':'' }}><span>需要申请，组长/管理员批准后才能加入</span>
                                                </li>
                                                <li><input type="radio" name="join_way"
                                                           value="2" {{ $group->join_way==2?'checked':'' }}><span>不允许任何人加入</span>
                                                </li>
                                            </ul>
                                            <input type="submit" name="" value="确认" class="add-friendsh-go">
                                            <input type="button" name="" value="取消" class="add-friendship-cancel"
                                                   onclick="$('.member-add-mode').slideUp()">
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                @elseif($opt=='log')
                    <div class="log">
                        <div class="select">
                            <select name="">
                                <option value="">全部</option>
                                <option value="3" {{ request()->input('type', NULL) == 3?"selected":"" }}>小组讨论管理</option>
                                <option value="2" {{ request()->input('type', NULL) == 2?"selected":"" }}>成员管理</option>
                                <option value="1" {{ request()->input('type', NULL) == 1?"selected":"" }}>小组管理</option>
                            </select>
                        </div>
                        @foreach( $group->getLogs(request()->input('type', NULL)) as $date=>$logs )
                        <div class="day-log">
                            <div class="date">
                                {{ $date }}
                            </div>
                            <ul>
                                @foreach( $logs as $key=>$log )
                                <li>{!! $log->content !!}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="main-right">
                @if( $opt=='base-info' )
                    <div class="opt go-group">
                        <a href="/group/{{ $group->id }}" title="">&gt;回到{{ $group->name }}</a>
                    </div>
                @elseif($opt=='member-edit')
                    <div class="group-info-in">
                        <div class="group-top">
                            <img src="{{ $group->icon }}" alt="">
                            <div>
                                <a href="#" title="">{{ $group->name }}</a>
                                <p>
                                    我是小组的{{ session('uid') && $group->allMember->find(session('uid'))->pivot->role==2?'组长':'管理员' }}</p>
                            </div>
                        </div>
                    </div>
                    <h4 class="member-search-tips">小组成员搜索</h4>
                    <div class="member-search">
                        <form action="" method="get" accept-charset="utf-8">
                            <input type="hidden" name="gid" value="{{ $group->id }}">
                            <input type="text" name="name" value="{{ request('name', '') }}" placeholder="名号" class="search-in">
                            <input type="submit" name="" value="搜索成员" class="search-go">
                        </form>
                    </div>
                    <h4 class="member-edit-tips">管理员任免</h4>
                    <p class="member-edit-tips">提拔成员为管理员， 点击成员名号旁边^;<br/>免去当前管理员的责权，点击名号旁边 v。<br/>管理员能够管理小组发言。</p>
                    <h4 class="member-edit-tips">踢人和封禁</h4>
                    <p class="member-edit-tips">把成员踢出小组， 点击头像旁边k。 踢出去的用户以后可以再加入。<br/>永久禁止一个成员加入本小组， 点击头像旁边x。</p>
                @elseif($opt=='data-statistics')
                    <div class="opt go-group">
                        <a href="/group/{{ $group->id }}" title="">&gt;回到{{ $group->name }}</a>
                    </div>
                @elseif($opt=='ban-manage')
                    <h4 class="ban-manage-tips">设置违禁词的意义</h4>
                    <p class="ban-manage-tips">管理员可以通过在左侧设置违禁词(例如广告等)，帮助小组讨论的管理，有效控制与小组内容不相关的内容。</p>
                    <div class="opt go-group">
                        <a href="/group/{{ $group->id }}" title="">&gt;回到{{ $group->name }}</a>
                    </div>
                @elseif($opt=='other-set')
                    <div class="opt go-group">
                        <a href="/group/{{ $group->id }}" title="">&gt;回到{{ $group->name }}</a>
                    </div>
                @elseif($opt=='log')
                    <h4 class="log-tips">小组管理日志</h4>
                    <p class="log-tips">小组管理日志是对组长和管理员在小组中进行各种管理操作的日志存档，在此能够方便的查阅最近60天的历史管理操作行为。</p>
                    <div class="opt go-group">
                        <a href="/group/{{ $group->id }}" title="">&gt;回到{{ $group->name }}</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop