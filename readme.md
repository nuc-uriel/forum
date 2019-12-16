## 介绍
<p>
    本程序为中北大学2015级毕业设计《基于LAMP架构的论坛系统的设计与实现》
</p>

## 配置
linux+apache+php5.6+mysql+laravel5.4

## 支持软件

- elasticsearch-5.1.1
- redis

## 支持服务

- elasticsearch-5.1.1\bin\elasticsearch(本机位于：D:\PHP\elasticsearch-5.1.1\bin\elasticsearch.bat)

- redis-server(本机位于：D:\PHP\redis-64.2.8.2101\redis-server.exe D:\PHP\redis-64.2.8.2101\redis.windows.conf)

- laravel-echo-server start(进入当前项目目录，配置文件位于当前目录下)

- php artisan queue:work --queue=my-broadcast,email(消费队列，进入当前项目目录)

## docker启动流程
1. docker-compose up -d
2. docker-compose exec app composer install
3. docker-compose exec app php artisan migrate
4. docker-compose exec laravel-echo-server yarn
5. docker-compose exec app php artisan queue:work --queue=my-broadcast,email

