# syntax=docker/dockerfile:1
FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git unzip libyaml-dev libzip-dev \
    && docker-php-ext-install zip \
    && pecl install yaml pcov apcu  \
    && docker-php-ext-enable yaml pcov apcu \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json ./
RUN composer install --no-interaction --prefer-dist --no-scripts || true

COPY . .

RUN chmod +x bin/r4policy

ENV PATH="/app/bin:${PATH}"

CMD [ "bash" ]
