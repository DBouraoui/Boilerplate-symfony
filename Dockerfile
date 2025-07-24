FROM dunglas/frankenphp:1.2-php8.2-alpine

WORKDIR /app

# Dépendances PHP pour PostgreSQL et Composer
RUN apk add --no-cache postgresql-dev gcc g++ make autoconf libc-dev bash curl \
    && docker-php-ext-install pdo_pgsql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . .
