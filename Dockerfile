FROM php:8.2-apache

# Install MySQL drivers
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache rewrite
RUN a2enmod rewrite

# Copy app
COPY . /var/www/html/

# Fix permissions (important)
RUN chown -R www-data:www-data /var/www/html
