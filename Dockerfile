# ==========================================
# Étape 1 : Build du Frontend (Node.js + PHP pour Wayfinder)
# ==========================================
FROM node:20-alpine AS frontend-builder

# Installation de PHP et des extensions minimales pour que les commandes Artisan fonctionnent pendant le build Vite
RUN apk add --no-cache php php-cli php-mbstring php-openssl php-tokenizer php-xml php-ctype php-fileinfo

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

# Installation des extensions PHP nécessaires pour la production
RUN install-php-extensions intl bcmath pdo_pgsql pdo_mysql gd

COPY --chown=www-data:www-data . /var/www/html

# Récupération des assets compilés par Vue.js depuis l'étape 1
COPY --from=frontend-builder --chown=www-data:www-data /app/public/build /var/www/html/public/build

USER www-data

# Installation des dépendances Composer en mode production
RUN composer install --prefer-dist --optimize-autoloader --no-dev --no-interaction

# Nettoyage du cache Composer
RUN rm -rf /var/www/html/.composer/cache
