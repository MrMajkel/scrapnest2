FROM php:8.2-fpm

# Instalacja zależności systemowych i rozszerzeń PHP
RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql pgsql

# Ustawienie katalogu roboczego
WORKDIR /var/www/html

# (opcjonalnie) Skopiuj pliki jeśli budujesz obraz z kodem
# COPY . /var/www/html
