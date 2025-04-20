FROM php:8.2-apache

# Mise à jour des packages système
RUN apt-get update && apt-get upgrade -y \
    && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP (avec versions vérifiées)
RUN docker-php-ext-install pdo pdo_mysql opcache