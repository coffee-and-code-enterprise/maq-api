FROM php:8.1-apache

# Instala dependências e extensões necessárias (PDO MySQL, GD)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    zip \
    unzip \
  && docker-php-ext-configure gd --with-jpeg --with-freetype \
  && docker-php-ext-install pdo pdo_mysql gd

# Configura document root para a pasta /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
ENV PORT 8080

RUN sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
  && sed -ri "s!DocumentRoot /var/www/html!DocumentRoot ${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
  && sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf \
  && sed -i "s/<VirtualHost \\*:80>/<VirtualHost \\*:${PORT}>/" /etc/apache2/sites-available/000-default.conf \
  && a2enmod rewrite

# Copia o código da aplicação
COPY . /var/www/html

# Permissões (uploads)
RUN mkdir -p /var/www/html/public/uploads && chown -R www-data:www-data /var/www/html/public/uploads || true

EXPOSE 8080

# Start Apache em foreground
CMD ["apache2-foreground"]
