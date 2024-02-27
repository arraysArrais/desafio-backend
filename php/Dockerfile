FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar dependêncioas
RUN composer install

# Gerar APP_KEY
RUN php artisan key:generate

# Gerar chave JWT
RUN php artisan jwt:secret 

# Rodar migrations
RUN php artisan migrate --force

# Rodar seeders
RUN php artisan db:seed
