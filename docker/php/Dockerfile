# ./docker/php/Dockerfile
FROM php:7.2-fpm
RUN apt-get update && apt-get install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libmcrypt-dev \
		libpng-dev \
	&& docker-php-ext-install -j$(nproc) iconv\
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) gd

RUN pecl install mcrypt-1.0.2
RUN docker-php-ext-enable mcrypt
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install mbstring
RUN pecl install apcu-5.1.8
RUN docker-php-ext-enable apcu

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && mv composer /usr/local/bin/composer


WORKDIR /usr/src/app

COPY . /usr/src/app

#RUN chown -R www-data:www-data /usr/src/app

#USER www-data

RUN chmod -R 777 var/cache
RUN chmod -R 777 var/log

RUN PATH=$PATH:/usr/src/app/vendor/bin:bin