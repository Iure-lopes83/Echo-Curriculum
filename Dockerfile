FROM php:8.2-apache

# Instalar extensões do PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libmariadb-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Configurar o diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos
COPY . /var/www/html/

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Configurar Apache para servir index.php como padrão
RUN echo "DirectoryIndex index.php" >> /etc/apache2/apache2.conf

# Expor porta 80
EXPOSE 80

# Iniciar Apache
CMD ["apache2-foreground"]
