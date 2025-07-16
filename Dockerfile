FROM php:8.2-apache

# Installer les dépendances essentielles
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    gettext-base \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration Apache avec modules nécessaires pour Laravel
RUN a2enmod rewrite \
    && a2enmod headers \
    && a2enmod ssl
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Copier l'application
WORKDIR /var/www/html
COPY . .

# Installer les dépendances avec autoloader optimisé
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Script de démarrage - Copier au bon endroit pour Railway
RUN cp docker/start-minimal.sh /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# Railway utilise la variable PORT dynamiquement
EXPOSE $PORT

CMD ["/usr/local/bin/start.sh"]
