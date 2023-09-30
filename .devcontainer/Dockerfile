# Use an official PHP runtime as the base image with PHP 8.0
FROM php:8.0

# Install required extensions and libraries
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    unzip \
    zip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli zip

# Set up PHP configurations
RUN echo "date.timezone = 'Asia/Kolkata'" > /usr/local/etc/php/php.ini
RUN echo "memory_limit = 512M" >> /usr/local/etc/php/php.ini
