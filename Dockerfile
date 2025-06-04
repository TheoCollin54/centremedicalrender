FROM php:8.1-apache

# Copie les fichiers dans le conteneur
COPY . /var/www/html/

# Active mod_rewrite pour Apache
RUN a2enmod rewrite

# Donne les bons droits
RUN chown -R www-data:www-data /var/www/html

# Port exposé
EXPOSE 80
