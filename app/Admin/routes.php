<?php

use Illuminate\Routing\Router;

Admin::registerHelpersRoutes();

Route::group([
    'prefix'        => config('admin.prefix'),
    'namespace'     => Admin::controllerNamespace(),
    'middleware'    => ['web', 'admin'],
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->group(['prefix'=>'users'], function (Router $router) {
        $router->match(['get', 'post'], 'ban', 'UserController@ban');
        $router->get('show/{id}', 'UserController@show');
    });
    $router->group(['prefix'=>'groups'], function (Router $router) {
        $router->match(['get', 'post'], 'ban', 'GroupController@ban');
        $router->get('show/{id}', 'GroupController@show');
        $router->get('apply', 'GroupController@apply');
        $router->match(['get', 'post'], 'reset/{id}', 'GroupController@reset');
    });
    $router->group(['prefix'=>'topics'], function (Router $router) {
        $router->match(['get', 'post'], 'ban', 'TopicController@ban');
    });
    $router->group(['prefix'=>'reports'], function (Router $router) {
        $router->get('show/{id}', 'ReportController@show');
    });
    $router->resource('/users', UserController::class);
    $router->resource('/groups', GroupController::class);
    $router->resource('/topics', TopicController::class);
    $router->resource('/comments', CommentController::class);
    $router->resource('/types', GroupTypeController::class);
    $router->resource('/reports', ReportController::class);
});
