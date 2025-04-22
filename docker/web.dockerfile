FROM php:8.2-apache

RUN true \
    && apt-get update \
    && a2enmod rewrite \
    && apt-get install -qqy libxml2-dev libxslt1.1 libxslt1-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/  \
    && docker-php-ext-install -j$(nproc) iconv  xsl mysqli gd

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN apt-get install nano

COPY ./php/php.ini /usr/local/etc/php/php.ini
COPY ./apache2/site.conf /etc/apache2/sites-enabled/site.conf

RUN chown -R www-data /var/www
RUN true \
    && chown -R www-data /var/www \
    && mkdir -p /tmp/sessions \
    && chown -R www-data /tmp/sessions

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin


ENV PHP_SESSION_SAVE_PATH=/tmp/sessions
ENV PHP_SESSION_SAVE_HANDLER=files

CMD ["apache2-foreground"]
WORKDIR /var/www
