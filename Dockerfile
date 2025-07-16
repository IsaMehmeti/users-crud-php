# Use official PHP image with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . /var/www/html

# Fix git ownership issue for Docker
RUN git config --global --add safe.directory /var/www/html

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configure Apache
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate application key and JWT secret
RUN php artisan key:generate --no-interaction
RUN php -r "echo 'JWT_SECRET=' . base64_encode(random_bytes(32)) . PHP_EOL;" >> .env

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"] 