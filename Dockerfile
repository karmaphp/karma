FROM php:7.2-fpm

RUN apt-get update && \
    apt-get install -y \
        git \
        zip \
        unzip

# Production container
# RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
# Development container
RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /code
