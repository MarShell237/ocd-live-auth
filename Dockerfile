# ==========================================
# Étape 1 : Build du frontend (PHP + Node)
# ==========================================
FROM serversideup/php:8.5-cli AS frontend-builder

USER root

# Node.js depuis l'image officielle
COPY --from=node:22-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
 && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

RUN install-php-extensions intl bcmath gd

WORKDIR /app

# Dépendances PHP (Wayfinder a besoin de vendor/ + artisan)
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-interaction --no-scripts

# Dépendances JS
COPY package*.json ./
RUN npm ci

# Sources + .env minimal pour que artisan démarre
COPY . .
RUN composer dump-autoload --optimize \
 && cp .env.example .env \
 && php artisan key:generate --force

RUN npm run build

# ==========================================
# Étape 2 : Image de production
# ==========================================
FROM serversideup/php:8.5-frankenphp

USER root
RUN install-php-extensions intl bcmath pdo_pgsql pdo_mysql gd

COPY --chown=www-data:www-data . /var/www/html
COPY --from=frontend-builder --chown=www-data:www-data /app/public/build /var/www/html/public/build

USER www-data
RUN composer install --prefer-dist --optimize-autoloader --no-dev --no-interaction
