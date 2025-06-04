FROM php:8.1-apache

# Installe les dépendances nécessaires à PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Active mod_rewrite pour Apache
RUN a2enmod rewrite

# Copie les fichiers dans le conteneur
COPY . /var/www/html/

# Donne les bons droits
RUN chown -R www-data:www-data /var/www/html

# Port exposé
EXPOSE 80
