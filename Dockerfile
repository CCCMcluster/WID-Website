FROM php:7.4-fpm-alpine3.12

RUN apk add --no-cache --virtual .build-deps \
    coreutils \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd \
      --with-freetype \
      --with-jpeg=/usr/include \
    && docker-php-ext-install -j "$(nproc)" \
      gd \
      opcache \
      pdo_mysql \
      zip \
      bcmath \
      soap \
    && pecl install redis-3.1.1 \
    && docker-php-ext-enable redis \
    && apk add --no-cache \
      libpng \
      libjpeg \
      libpq \
      libxml2 \
      git \
      # for drush command
      mysql-client \
    ; \
    runDeps="$( \
            scanelf --needed --nobanner --format '%n#p' --recursive /usr/local \
              | tr ',' '\n' \
              | sort -u \
              | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )"; \
    apk add --virtual .drupal-phpexts-rundeps $runDeps; \
    apk del .build-deps

RUN touch /usr/local/etc/php/conf.d/uploads.ini \
    && echo "upload_max_filesize = 512M;\npost_max_size = 512M;" >> /usr/local/etc/php/conf.d/uploads.ini

RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/bin --filename=composer

RUN composer self-update 1.10.19

WORKDIR /var/www

ENTRYPOINT /var/www/docker/docker-entrypoint.prod.sh
