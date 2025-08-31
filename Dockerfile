# ---------- Build Stage ----------
FROM php:8.2-cli AS build

# Install build dependencies for PHP extensions & Composer
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    autoconf \
    make \
    g++ \
    bash \
    --no-install-recommends && rm -rf /var/lib/apt/lists/*

# Install PHP extensions needed by Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app


# Copy all application files (including artisan) before installing dependencies
COPY . .

# Ensure bootstrap/cache exists and is writable before composer install
RUN mkdir -p bootstrap/cache && chmod -R 775 bootstrap/cache

# Install dependencies without dev packages
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && composer clear-cache

# ---------- Runtime Stage ----------
FROM php:8.2-apache AS runtime

# Install only runtime dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    --no-install-recommends && rm -rf /var/lib/apt/lists/*
# Set environment variables for production
ENV APP_ENV=production \
    APP_DEBUG=false

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Point Apache to Laravel's public directory and allow overrides
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s|AllowOverride None|AllowOverride All|' /etc/apache2/apache2.conf \
    && sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/public>|' /etc/apache2/apache2.conf

# Optional: avoid "ServerName" warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy PHP extensions from build stage
COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=build /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Copy application from build stage
COPY --from=build /app ./

# Remove unnecessary files and set correct permissions
RUN rm -rf /var/www/html/tests /var/www/html/.git /var/www/html/.github /var/www/html/.gitignore /var/www/html/.gitattributes \
    /var/www/html/README* /var/www/html/CHANGELOG* /var/www/html/*.md \
    && chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
