FROM php:8.2-apache

# Install MySQL drivers
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache rewrite
RUN a2enmod rewrite

# Apache must listen on Railway PORT
ENV APACHE_RUN_PORT=8080
RUN sed -i 's/80/${APACHE_RUN_PORT}/g' /etc/apache2/ports.conf \
 && sed -i 's/:80/:${APACHE_RUN_PORT}/g' /etc/apache2/sites-enabled/000-default.conf

# Copy project
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html
