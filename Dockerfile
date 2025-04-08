# Usa una imagen base de PHP con Apache
FROM php:8.1-apache

# Instala extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copia todos los archivos al directorio del servidor web
COPY . /var/www/html/

# Configura el directorio público como raíz (si index.php está en public/)
##RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Da permisos adecuados
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Expone el puerto 80
EXPOSE 80
