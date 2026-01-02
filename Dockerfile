FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN a2enmod rewrite

ENV PORT=8080
RUN sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf \
 && sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-enabled/000-default.conf

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

CMD ["apache2-foreground"]