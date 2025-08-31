# Use the official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www/html

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Ensure bootstrap/cache exists and is writable
RUN mkdir -p bootstrap/cache && chmod -R 775 bootstrap/cache

# Install Laravel dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
