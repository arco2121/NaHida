# STAGE 1: Frontend
FROM node:20-alpine AS node_builder
WORKDIR /app

# Copia solo i file necessari alla gestione dei pacchetti
COPY package*.json ./

# Installazione pulita (inclusa la compilazione dei moduli nativi per Alpine)
RUN npm install

# Copia il resto dei file (Vite necessita anche di tailwind.config.js, vite.config.js, ecc.)
COPY . .

# Esegui la build compilando gli asset in public/build
RUN npm run build

# STAGE 2: PHP (usiamo CLI invece di Apache per evitare conflitti di log/porte)
FROM php:8.4-cli-bullseye

# Installazione dipendenze di sistema
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev libpq-dev zip unzip git curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Installazione Node e Concurrently
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs
RUN npm install -g concurrently

WORKDIR /var/www/html
COPY . .
COPY --from=node_builder /app/public/build ./public/build

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Permessi
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && chmod -R 775 /var/www/html/storage

# Usiamo la porta 10000 che Render si aspetta
EXPOSE 10000

# AVVIO:
# 1. Migrazioni
# 2. php artisan serve (Server Web sulla 10000)
# 3. Reverb (Websocket)
# 4. MQTT Client
CMD php artisan migrate:fresh && concurrently \
    "php artisan serve --host=0.0.0.0 --port=10000" \
    "php artisan reverb:start --host=0.0.0.0 --port=8080" \
    "php artisan mqtt:listen"
