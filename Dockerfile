# 1️⃣ Base estable PHP 8.2 + Apache
FROM php:8.2-apache-buster

# 2️⃣ Activar mpm_prefork y desactivar otros MPM
RUN a2dismod mpm_event \
 && a2enmod mpm_prefork rewrite

# 3️⃣ Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql

# 4️⃣ Configurar Apache para servir desde public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
 && sed -ri "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5️⃣ Copiar proyecto
COPY . /var/www/html/

# 6️⃣ Permisos
RUN chown -R www-data:www-data /var/www/html

# 7️⃣ Exponer puerto
EXPOSE 80

# 8️⃣ Arrancar Apache
CMD ["apache2-foreground"]
