FROM php:8.1-apache

COPY . /var/www/html/ 

RUN docker-php-ext-install pdo pdo_mysql  
RUN chown root:www-data *

EXPOSE 8084