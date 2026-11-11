FROM node:20-alpine AS frontend-builder

# PHP + extensions Laravel needs to boot artisan
RUN apk add --no-cache \
    php84 php84-phar php84-mbstring php84-openssl php84-tokenizer \
    php84-xml php84-dom php84-xmlwriter php84-simplexml php84-ctype \
    php84-fileinfo php84-session php84-pdo php84-iconv php84-curl \
    composer \
 && ln -sf /usr/bin/php84 /usr/bin/php

WORKDIR /app

# PHP deps first (cache-friendly)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist

# JS deps
COPY package*.json ./
RUN npm ci

# Full source, then boot-able env for artisan
COPY . .
RUN composer dump-autoload --no-dev --optimize \
 && cp .env.example .env \
 && php artisan key:generate --force

RUN npm run build
