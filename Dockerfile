FROM php:8.3-apache


RUN docker-php-ext-install \
    pdo \
    pdo_mysql

RUN a2enmod rewrite



COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html


COPY composer.json composer.lock* ./


RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Copy Apache virtual host configuration
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf


#ENV APACHE_DOCUMENT_ROOT=/var/www/html/app/public

# Copy application files
COPY app ./app
COPY config ./config
COPY scripts ./scripts

# Copy RDS CA certificate if present in build context
#COPY global-bundle.pem /etc/ssl/rds/global-bundle.pem

#RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    #/etc/apache2/sites-available/*.conf \
    #/etc/apache2/apache2.conf \
    #/etc/apache2/conf-available/*.conf

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
