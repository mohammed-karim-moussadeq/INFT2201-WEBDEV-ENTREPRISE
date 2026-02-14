FROM php:8.3.3-apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Add unzip and git here
RUN apt-get update && apt-get install -y libpq-dev unzip git && docker-php-ext-install pdo pdo_pgsql pgsql

COPY ./Docker/000-default.conf /etc/apache2/sites-available/000-default.conf
WORKDIR /var/www/html