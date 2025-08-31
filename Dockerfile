# ---------- Build Stage ----------
FROM php:8.2-cli-alpine AS build

# Install build dependencies for PHP extensions & Composer
RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    autoconf \
    make \
    g++ \
    bash

# Install PHP extensions needed by Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install dependencies without dev packages
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Copy the rest of the application
COPY . .

# Ensure bootstrap/cache exists and is writable
RUN mkdir -p bootstrap/cache && chmod -R 775 bootstrap/cache

# ---------- Runtime Stage ----------
FROM php:8.2-apache-alpine

# Install only runtime dependencies
RUN apk add --no-cache \
    libpng \
    libzip \
    oniguruma \
    libxml2

# Enable Apache mod_rewrite
RUN sed -i '/LoadModule rewrite_module/s/^#//g' /etc/apache2/httpd.conf

# Set working directory
WORKDIR /var/www/html

# Copy PHP extensions from build stage
COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=build /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Copy application from build stage
COPY --from=build /app ./

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["httpd", "-D", "FOREGROUND"]
