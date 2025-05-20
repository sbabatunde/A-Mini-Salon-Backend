# Use PHP with necessary extensions
FROM php:8.2-cli

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libjpeg-dev libonig-dev libxml2-dev \
    npm nodejs \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app files
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev \
    && npm install && npm run build

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Expose Laravel port
EXPOSE 8000

# Entrypoint to handle migrations, storage link, and start server
CMD php artisan config:cache && \
    php artisan migrate --force && \
    php artisan storage:link && \
    php artisan serve --host=0.0.0.0 --port=8000
