FROM php:7.4-apache

RUN apt-get update \
    && apt-get install --yes --force-yes cron g++ gettext \
                        libicu-dev openssl libc-client-dev libkrb5-dev libxml2-dev libfreetype6-dev \
                        libgd-dev libmcrypt-dev bzip2 libbz2-dev libtidy-dev libcurl4-openssl-dev \
                        libz-dev libmemcached-dev libxslt-dev \
    && a2enmod rewrite \
    && docker-php-ext-install bcmath && docker-php-ext-enable bcmath \
    && docker-php-ext-install curl && docker-php-ext-enable curl \
    && docker-php-ext-install fileinfo && docker-php-ext-enable fileinfo \
    && docker-php-ext-install intl && docker-php-ext-enable intl \
    && apt-get update -y && apt-get install -y libldap2-dev libldb-dev && docker-php-ext-install ldap && docker-php-ext-enable ldap \
    && apt-get update && apt-get install -y libonig-dev && docker-php-ext-install -j$(nproc) mbstring && docker-php-ext-enable mbstring \
    && docker-php-ext-install mysqli && docker-php-ext-enable mysqli \
    && docker-php-ext-install pdo && docker-php-ext-enable pdo \
    && docker-php-ext-install pdo_mysql && docker-php-ext-enable pdo_mysql \
    && docker-php-ext-install opcache && docker-php-ext-enable opcache \
    && docker-php-ext-configure gd --with-freetype=/usr --with-jpeg=/usr && docker-php-ext-install gd \
    && docker-php-ext-install xml && docker-php-ext-enable xml \
    && docker-php-ext-install posix \
    && apt-get update && apt-get install -y libzip-dev zip && docker-php-ext-install zip && docker-php-ext-enable zip \
    && rm -rf /var/lib/apt/lists/*

# Copy the proper apache configuration for this app to the container
COPY ./000-default.conf /etc/apache2/sites-enabled/000-default.conf
