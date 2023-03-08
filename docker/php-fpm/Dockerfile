FROM php:8.1-fpm

WORKDIR /var/www/html

RUN apt update && \
    apt -qy install \
    vim \
    zlib1g-dev \
    libmemcached-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    zip \
    unzip \
    git \
    && pecl install xdebug \
    && pecl install apcu \
    && pecl install memcached \
    && docker-php-ext-install pcntl zip curl

RUN docker-php-ext-enable memcached apcu \
	&& apt-get clean \
	&& rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

# Clear cach
RUN rm -rf /var/cache/apk/*

#RUN usermod -u 1000 www-data

#COPY --chown=www-data:www-data . /var/www/html

#USER www-data

EXPOSE 9000
CMD ["php-fpm"]
