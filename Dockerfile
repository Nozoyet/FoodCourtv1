# 1️⃣ Base PHP + Apache
FROM php:8.2-apache

# 2️⃣ Desactivar mpm_event y activar prefork (requisito PHP)
RUN a2dismod mpm_event \
 && a2enmod mpm_prefork

# 3️⃣ Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# 4️⃣ Copiar todo el proyecto al contenedor
COPY . /var/www/html/

# 5️⃣ Configurar Apache para que sirva desde public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 6️⃣ Dar permisos correctos a www-data
RUN chown -R www-data:www-data /var/www/html

# 7️⃣ Exponer el puerto 80
EXPOSE 80

# 8️⃣ Comando por defecto (ya viene con Apache)
CMD ["apache2-foreground"]
