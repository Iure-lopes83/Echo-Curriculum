# Usar imagem oficial do PHP com Apache
FROM php:8.2-apache

# Instalar extensões do PHP necessárias
RUN docker-php-ext-install pdo_mysql mysqli

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Configurar o diretório de trabalho
WORKDIR /var/www/html

# Copiar todos os arquivos do projeto
COPY . /var/www/html/

# Dar permissões corretas
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Configurar o Apache para servir o index.php da raiz
RUN echo "DirectoryIndex index.php" >> /etc/apache2/apache2.conf

# Expor a porta 80
EXPOSE 80

# Comando para iniciar o Apache
CMD ["apache2-foreground"]
