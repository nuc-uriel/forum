## 介绍
#### 本程序为中北大学2015级毕业设计《基于LAMP架构的论坛系统的设计与实现》

## 配置
- linux
- nginx 1.10
- php 5.6
- mysql 5.7.15
- elasticsearch 6.8.5
- redis
- laravel 5.4

## 主机启动流程
```shell script
elasticsearch-5.1.1\bin\elasticsearch(本机位于：D:\PHP\elasticsearch-5.1.1\bin\elasticsearch.bat)
redis-server(本机位于：D:\PHP\redis-64.2.8.2101\redis-server.exe D:\PHP\redis-64.2.8.2101\redis.windows.conf)
laravel-echo-server start(进入当前项目目录，配置文件位于当前目录下)
php artisan queue:work --queue=my-broadcast,email(消费队列，进入当前项目目录)
```

## docker启动流程
```shell script
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php artisan migrate
docker-compose exec app php artisan es:init
docker-compose exec laravel-echo-server yarn
docker-compose exec app php artisan queue:work --queue=my-broadcast,email &
```

## tips
- 如果es内存分配不够异常退出，则需要：
   1. **/etc/sysctl.conf** 添加 `vm.max_map_count=262144`
   2. `sudo sysctl -p`
   
- 如果es数据表不同步，则需要：
   ```shell script
      docker-compose exec app php artisan scout:import "App\User"
      docker-compose exec app php artisan scout:import "App\Group"
      docker-compose exec app php artisan scout:import "App\Topic"
   ```