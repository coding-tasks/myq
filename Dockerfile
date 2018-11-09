FROM php:7.1-fpm
MAINTAINER Ankit Pokhrel <hello@ankit.pl>

ENV LANG C.UTF-8
ENV DEBIAN_FRONTEND noninteractive

RUN ln -sf /usr/share/zoneinfo/Asia/Kathmandu /etc/localtime
RUN apt-get update && apt-get install -y \
        zlib1g-dev \
        git \
    && docker-php-ext-install -j$(nproc) zip

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && chmod +x /usr/local/bin/composer

COPY ./docker-entrypoint.sh /usr/bin/entrypoint.sh
RUN chmod +x /usr/bin/entrypoint.sh

WORKDIR /var/www

EXPOSE 80
