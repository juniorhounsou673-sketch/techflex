# ---------- Étape 1 : compilation des assets (Vite 8 / Tailwind 4) ----------
FROM node:22-alpine AS frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- Étape 2 : application PHP ----------
FROM php:8.3-cli-bookworm

RUN apt-get update && apt-get install -y \
    git curl unzip libpng-dev libonig-dev libxml2-dev libzip-dev libsqlite3-dev libicu-dev \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Autorise les photos produits jusqu'à 50 Mo (validation Laravel : max:51200).
RUN printf 'upload_max_filesize=64M\npost_max_size=70M\nmemory_limit=256M\nmax_execution_time=120\n' \
    > /usr/local/etc/php/conf.d/zz-techflex.ini

WORKDIR /app

COPY . .
COPY --from=frontend /app/public/build /app/public/build

# Téléchargements séquentiels : évite les 504 temporaires de l'API GitHub
# lors d'un composer install avec beaucoup de paquets en parallèle.
ENV COMPOSER_MAX_PARALLEL_DOWNLOADS=1
ENV COMPOSER_PROCESS_TIMEOUT=600

RUN (composer install --no-dev --optimize-autoloader --no-interaction --no-progress \
     || composer install --no-dev --optimize-autoloader --no-interaction --no-progress \
     || composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-source) \
    && mkdir -p database storage/framework/sessions storage/framework/views \
               storage/framework/cache/data storage/logs bootstrap/cache \
    && touch database/database.sqlite \
    && chmod -R 775 storage bootstrap/cache database

ENV PORT=8080
EXPOSE 8080

# Au démarrage : adapte l'URL publique, prépare la base SQLite, charge les
# données de démonstration une seule fois, puis lance le serveur sur $PORT.
CMD export APP_URL="${RENDER_EXTERNAL_URL:-http://localhost}" && \
    touch database/database.sqlite && \
    php artisan migrate --force && \
    (php artisan db:seed --force || true) && \
    (php artisan storage:link || true) && \
    php artisan optimize && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
