FROM php:8.1-apache

# Installe les extensions nécessaires pour PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Active mod_rewrite pour Apache
RUN a2enmod rewrite

# Copie les fichiers APRÈS l'installation
COPY . /var/www/html/

# Donne les bons droits
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
