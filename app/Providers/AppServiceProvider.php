<?php

namespace App\Providers;

use App\Comment;
use App\GroupLog;
use App\Inform;
use App\Message;
use App\Topic;
use Elasticsearch\ClientBuilder as ElasticBuilder;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;
use App\Extend\Elasticsearch\ElasticsearchEngine;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 注册es引擎
        resolve(EngineManager::class)->extend('elasticsearch', function ($app) {
            return new ElasticsearchEngine(ElasticBuilder::create()
                ->setHosts(config('scout.elasticsearch.config.hosts'))
                ->build());
        });

        // 验证文件是否存在
        Validator::extend('file_exists', function ($attribute, $value, $parameters, $validator) {
            return file_exists($parameters[0]($value));
        });

        // 注册观察者
        GroupLog::observe(\App\Observers\GroupLogObserver::class);
        Inform::observe(\App\Observers\InformObserver::class);
        Message::observe(\App\Observers\MessageObserver::class);
        Topic::observe(\App\Observers\TopicObserver::class);
        Comment::observe(\App\Observers\CommentObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
