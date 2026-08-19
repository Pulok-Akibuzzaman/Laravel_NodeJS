FROM php:8.4-cli

# Install system dependencies & SQLite drivers
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    zip \
    unzip \
    git \
    curl \
    ca-certificates \
    && docker-php-ext-install pdo_sqlite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app/laravel-app

# Copy application files
COPY laravel-app/ /app/laravel-app

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Ensure SQLite database exists & generate application key
RUN touch database/database.sqlite
RUN php artisan key:generate || true

EXPOSE 8000

# Execute migration on container startup and serve application
CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
