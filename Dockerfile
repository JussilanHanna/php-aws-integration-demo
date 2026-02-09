FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libcurl4-openssl-dev \
  && docker-php-ext-install pdo_mysql zip curl \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . /app

RUN composer install --no-interaction --no-progress --prefer-dist

EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
