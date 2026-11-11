# ==========================================
# Étape 1 : Build du Frontend (Vue.js / Vite)
# ==========================================
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# ==========================================
# Étape 2 : Build de l'Application PHP / FrankenPHP
# ==========================================
FROM serversideup/php:8.4-frankenphp

USER root

# Installation des extensions PHP nécessaires (ajoutez gd, pdo_pgsql ou pdo_mysql selon votre base de données)
RUN install-php-extensions intl bcmath pdo_pgsql pdo_mysql gd

COPY --chown=www-data:www-data . /var/www/html

# Récupération des assets compilés par Vue.js depuis l'étape 1
COPY --from=frontend-builder --chown=www-data:www-data /app/public/build /var/www/html/public/build

USER www-data

# Installation des dépendances Composer en mode production
RUN composer install --prefer-dist --optimize-autoloader --no-dev --no-interaction

# Nettoyage du cache Composer
RUN rm -rf /var/www/html/.composer/cache
