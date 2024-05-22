FROM php:7.4-fpm

RUN apt-get -y update && apt-get -y install libicu-dev libonig-dev
RUN docker-php-ext-install pdo pdo_mysql intl