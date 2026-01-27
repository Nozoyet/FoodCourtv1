# 1️⃣ Base estable PHP 8.2 + Apache
FROM php:8.2-apache-buster

# 2️⃣ Activar solo MPM Prefork y mod_rewrite (para URLs amigables)
#    Evitar errores de "More than one MPM loaded"
RUN a2dismod mpm_event || true \
 && a2enmod mpm_prefork rewrite

# 3️⃣ Instalar extensiones PHP necesarias
#    Incluyo mysqli y mbstring que suelen ser necesarias
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring

# 4️⃣ Configurar Apache para servir desde public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5️⃣ Copiar proyecto al contenedor
#    Uso chown directo para evitar problemas de permisos
COPY --chown=www-data:www-data . /var/www/html/

# 6️⃣ Exponer el puerto 80
EXPOSE 80

# 7️⃣ Arrancar Apache
CMD ["apache2-foreground"]
