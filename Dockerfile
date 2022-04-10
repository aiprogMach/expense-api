FROM php:8.1-fpm AS php_app_md_group

RUN apt-get update --allow-releaseinfo-change

# Core and utility packages
RUN apt-get install -y \
    git \
    zip \
    curl \
    sudo \
    unzip \
    libicu-dev \
    libbz2-dev \
    libpng-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libreadline-dev \
    libfreetype6-dev \
    g++ \
    libzip-dev \
    libmagickwand-dev \
    vim \
    unzip \
    git \
    curl \
    librabbitmq-dev

RUN docker-php-ext-install \
    bz2 \
    intl \
    iconv \
    bcmath \
    opcache \
    calendar \
    zip \
    exif \
    pdo \
    mysqli \
    pdo_mysql \
    gd \
    sockets


# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


WORKDIR /var/www/html

EXPOSE 9000
CMD ["php-fpm"]

# NGINX image
FROM nginx:alpine AS md-nginx

COPY . /var/www/html
# COPY ./nginx.conf /etc/nginx/conf.d/default.conf
