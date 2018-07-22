FROM php:7.1-apache

MAINTAINER "Isaac Daniel Batista <klonate@gmail.com>"

RUN apt-get update && apt-get install -y \
    git curl libmcrypt-dev libjpeg-dev libpng-dev libfreetype6-dev libbz2-dev && \
    apt-get clean && \
    docker-php-ext-install mcrypt pdo_mysql zip gd && \
    curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/app

ADD . /var/app

RUN composer install

USER root
