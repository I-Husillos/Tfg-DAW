FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    ca-certificates \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libicu-dev \
    libonig-dev \
    libssl-dev \
    gnupg2 \
    supervisor \
    procps \
    && rm -rf /var/lib/apt/lists/*


RUN pecl install redis \
&& docker-php-ext-enable redis

RUN docker-php-ext-configure zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    zip \
    gd \
    pdo_mysql \
    mysqli \
    xml \
    intl \
    bcmath \
    opcache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./.docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["php-fpm"]