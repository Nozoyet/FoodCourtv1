# 1️⃣ Base estable PHP 8.2 + Apache
FROM php:8.2-apache-bullseye

# 2️⃣ Forzar MPM prefork y activar mod_rewrite
RUN a2dismod mpm_event \
 && a2enmod mpm_prefork rewrite

# 3️⃣ Instalar extensiones necesarias para PHP
RUN docker-php-ext-install pdo pdo_mysql

# 4️⃣ Configurar Apache para servir desde public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5️⃣ Copiar todo el proyecto
COPY . /var/www/html/

# 6️⃣ Dar permisos correctos a Apache
RUN chown -R www-data:www-data /var/www/html

# 7️⃣ Exponer puerto web
EXPOSE 80

# 8️⃣ Comando por defecto (arranca Apache)
CMD ["apache2-foreground"]
