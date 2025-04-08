# Usa una imagen oficial de PHP con Apache
FROM php:8.1-apache

# Instala extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copia tus archivos PHP al directorio del servidor web
COPY . /var/www/html/

# Da permisos adecuados
RUN chown -R www-data:www-data /var/www/html