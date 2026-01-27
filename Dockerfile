FROM php:8.2-apache

RUN a2dismod mpm_event \
 && a2enmod mpm_prefork


# Extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Copiar todo el proyecto al contenedor
COPY . /var/www/html/

# Apache debe servir desde /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

