FROM php:5.6-fpm

RUN sed -i s/deb.debian.org/mirrors.aliyun.com/g /etc/apt/sources.list && \
    sed -i s/security.debian.org/mirrors.aliyun.com/g /etc/apt/sources.list

# 更新包
RUN apt-get update          \
    && apt-get install -y   \
       libfreetype6-dev     \
       libjpeg62-turbo-dev  \
       libpng-dev           \
       libxml2 libxml2-dev  \
       libpq-dev  \
       vim  \
       curl  \
       git

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo mysqli pdo_mysql \
    && docker-php-ext-configure soap --with-libxml-dir=/usr/include/ \
    && docker-php-ext-install  soap \
    && docker-php-ext-install zip \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install gettext \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install shmop \
    && docker-php-ext-install sockets \
    && docker-php-ext-install sysvsem \
    && docker-php-ext-install xmlrpc \
    && docker-php-ext-install pdo_pgsql

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
