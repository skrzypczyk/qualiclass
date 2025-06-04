FROM php:8.2-fpm

# Dépendances système
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev curl wget gnupg \
    libpq-dev libsqlite3-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libzip-dev \
    npm nodejs \
    && docker-php-ext-install pdo pdo_pgsql pdo_sqlite gd zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer Node.js 20 LTS
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Installer yarn (optionnel)
RUN npm install -g yarn

# Dossier de travail
WORKDIR /app


