FROM php:8.3-cli

# Install system dependencies & SQLite drivers
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_sqlite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application files
COPY . /app

# Install PHP dependencies ignoring strict platform requirements
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs

# Ensure SQLite database exists & run migrations
RUN touch database/database.sqlite
RUN php artisan key:generate || true

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
