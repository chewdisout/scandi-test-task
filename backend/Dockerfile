FROM php:8.1-apache

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

COPY . .

RUN rm /etc/apache2/sites-available/000-default.conf

RUN cat << 'APACHECONF' > /etc/apache2/sites-available/000-default.conf
<VirtualHost *:80>
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
    </Directory>
    FallbackResource /index.php
</VirtualHost>
APACHECONF
