# Copia a imagem base
FROM php:8.0-apache

# Indicar versao e descricao
LABEL version="1.0" description="Tomas-Goncalo/Latest"

# Copia os conteúdos do site para a imagem
COPY . /var/www/html/

# Adiciona ficheiro dos eventos e altera permissoes
RUN chown -R www-data:www-data /var/www/html/*

# Add the ServerName directive to Apache configuration
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Instalar mysqli
RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli
RUN apachectl restart

# Expoe os portos necessarios
EXPOSE 80
EXPOSE 3306
