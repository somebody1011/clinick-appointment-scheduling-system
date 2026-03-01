FROM php:8.2-apache

# Install PHP extensions (mysqli and pdo_mysql for database)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite

# Set document root
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose ports
EXPOSE 80 443

CMD ["apache2-foreground"]
