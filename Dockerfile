FROM php:8.2-cli

# System deps needed by Composer (zip downloads) + Git fallback
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
  && docker-php-ext-install pdo_mysql zip \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy app source
COPY . /app

# Install deps
RUN composer install --no-interaction --no-progress --prefer-dist

EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
