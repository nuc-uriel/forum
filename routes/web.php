<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(
    ['middleware' => 'web'], function () {
    Route::get('/', 'IndexController@index');
    Route::get('/index', 'IndexController@index');
    Route::get('/search/{type?}', 'IndexController@search');
    Route::get('/check_name', 'UserController@checkName');
    Route::post('/report', 'ReportController@report');
    Route::match(['get', 'post'], '/register', 'UserController@register');
    Route::match(['get', 'post'], '/login', 'UserController@login')->name('login');

    Route::group(['middleware' => 'auth.login'], function () {
        Route::get('/logout', 'UserController@logout');
    });

    Route::group(['prefix' => 'member'], function () {

        Route::group(['middleware' => 'auth.login'], function () {
            Route::get('/topics', 'UserController@topics');
            Route::get('/activate/send_email', 'UserController@sendEmailForActivate');
            Route::get('/signature/set', 'UserController@setSignature');
            Route::get('/places/get', 'UserController@getPlaces');
            Route::post('/head/set', 'UserController@setHeadPortrait');
            Route::post('/head/save', 'UserController@saveHeadPortrait');
            Route::post('/introduce/set', 'UserController@setIntroduce');
            Route::post('/update', 'UserController@updateMemberInfo');
            Route::match(['get', 'post'], '/password/update', 'UserController@updatePassword');

            Route::get('/idol/add', 'MemberRelationshipController@addIdol');
            Route::get('/idol/del', 'MemberRelationshipController@delIdol');
            Route::get('/blacklist/add', 'MemberRelationshipController@addBlackList');
            Route::get('/blacklist/del', 'MemberRelationshipController@delBlackList');
        });

        Route::get('/activate', 'UserController@activate');
        Route::match(['get', 'post'], '/password/reset/1', 'UserController@resetPassword1');
        Route::match(['get', 'post'], '/password/reset/2', 'UserController@resetPassword2');
        Route::get('/{opt?}', 'UserController@show');
    });

    Route::group(['prefix' => 'group'], function () {
        Route::get('/{id}', 'GroupController@show')->where('id', '[0-9]+');
        Route::get('/associational_groups', 'GroupController@getAssociationalGroups');
        Route::get('/topics', 'GroupController@topics');
        Route::get('/search', 'GroupController@search');
        Route::get('/members', 'GroupController@members');

        Route::group(['middleware' => 'auth.login'], function () {
            Route::get('/join', 'GroupMemberController@join');
            Route::get('/quit', 'GroupMemberController@quit');
            Route::post('/icon/set', 'GroupController@setIcon');
            Route::post('/icon/save', 'GroupController@saveIcon');
            Route::match(['get', 'post'], '/build', 'GroupController@build');

            Route::group(['middleware' => 'auth.group'], function () {
                Route::get('/chart', 'GroupController@getChart');
                Route::get('/chart/all', 'GroupController@getAllChart');
                Route::get('/set_join_way', 'GroupController@setJoinWay');
                Route::match(['get', 'post'], '/edit/{opt?}', 'GroupController@edit');

                Route::group(['prefix' => 'friends'], function () {
                    Route::get('/add', 'GroupFriendshipController@add');
                    Route::get('/del', 'GroupFriendshipController@del');
                });
                Route::group(['prefix' => 'member'], function () {
                    Route::get('/admin/appoint', 'GroupMemberController@appointAdmin');
                    Route::get('/admin/revocation', 'GroupMemberController@revocationAdmin');
                    Route::get('/apply/pass', 'GroupMemberController@passApply');
                    Route::get('/apply/refuse', 'GroupMemberController@refuseApply');
                    Route::get('/del', 'GroupMemberController@delMember');
                    Route::get('/blacklist/add', 'GroupMemberController@addBlacklist');
                    Route::get('/blacklist/del', 'GroupMemberController@delBlacklist');
                    Route::get('/leader/set', 'GroupMemberController@setLeader');
                });
                Route::group(['prefix' => 'ban'], function () {
                    Route::get('/add', 'GroupBanController@add');
                    Route::get('/del', 'GroupBanController@del');
                });
            });
        });
    });

    Route::group(['prefix' => 'inform', 'middleware' => 'auth.login'], function () {
        Route::get('list', 'InformController@showList');
        Route::get('del/{type}', 'InformController@del');
        Route::get('/{type}', 'InformController@show');
        Route::get('/{result}/{code}', 'InformController@dispose');
    });

    Route::group(['prefix' => 'chat', 'middleware' => 'auth.login'], function () {
        Route::get('{uid}', 'MessageController@show')->where('uid', '[0-9]+');
        Route::get('list', 'MessageController@showList');
        Route::get('read', 'MessageController@read');
        Route::get('del/{uid}', 'MessageController@del');
        Route::post('send', 'MessageController@send');
    });

    Route::group(['prefix' => 'topic'], function () {
        Route::get('{tid}', 'TopicController@show')->where('tid', '[0-9]+');

        Route::group(['middleware' => 'auth.login'], function () {
            Route::get('del', 'TopicController@del');
            Route::get('is_top/{is_top}', 'TopicController@isTop');
            Route::get('can_comment/{can_comment}', 'TopicController@canComment');
            Route::get('ban/{ban}', 'TopicController@ban');
            Route::match(['get', 'post'], '/add', 'TopicController@add');
            Route::match(['get', 'post'], '/edit', 'TopicController@edit');

            Route::group(['prefix' => 'comment'], function () {
                Route::post('add', 'CommentController@add');
                Route::get('del/{cid}', 'CommentController@del');
            });

            Route::group(['prefix' => 'like'], function () {
                Route::get('add/{target}', 'LikeController@add');
                Route::get('del/{target}', 'LikeController@del');
            });

            Route::group(['prefix' => 'collect'], function () {
                Route::get('add/{target}', 'CollectController@add');
                Route::get('del/{target}', 'CollectController@del');
            });
        });
    });
});

//Route::get('test', function (Request $request) {
//    $msg = '我是消息';
//    event(new \App\Events\SomeEvent($msg)); // 触发事件
//    return Response::make('触发事件');
//});

