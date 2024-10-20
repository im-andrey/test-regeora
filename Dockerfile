# Указываем базовый образ
FROM php:8.2-fpm

# Устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip

# Устанавливаем расширение Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Устанавливаем рабочую директорию
WORKDIR /var/www

# Копируем файлы проекта
COPY . .

# Устанавливаем зависимости Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Копируем файл конфигурации для Nginx
COPY ./docker/nginx.conf /etc/nginx/conf.d/default.conf

# Команда по умолчанию
CMD ["php-fpm"]
