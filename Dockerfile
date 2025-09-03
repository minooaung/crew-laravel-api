# ---------- Build Stage ----------
FROM php:8.2-cli AS build

# Install build dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libzip-dev libonig-dev libxml2-dev autoconf make g++ bash \
    --no-install-recommends && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy all application files
COPY . .

# Ensure Laravel directories exist
RUN mkdir -p bootstrap/cache storage/framework/{views,sessions,cache}

# Install dependencies with dev packages (for local/testing)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# ---------- Runtime Stage ----------
FROM php:8.2-apache AS runtime

# Install runtime dependencies
RUN apt-get update && apt-get install -y libpng-dev libzip-dev libonig-dev libxml2-dev \
    --no-install-recommends && rm -rf /var/lib/apt/lists/*

# Environment variables
ENV APP_ENV=local \
    APP_DEBUG=true

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Apache config
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s|AllowOverride None|AllowOverride All|' /etc/apache2/apache2.conf \
    && sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/public>|' /etc/apache2/apache2.conf \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy PHP extensions and app from build stage
COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=build /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=build /app ./

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
