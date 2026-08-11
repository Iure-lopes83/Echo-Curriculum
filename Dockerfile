FROM php:8.2-apache

# Instalar extensões do PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libmariadb-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar arquivos
COPY . /var/www/html/

# Permissões
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
