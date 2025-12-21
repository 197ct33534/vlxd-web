FROM php:8.4-apache

# # Install required PHP extensions and system dependencies
# RUN apt-get clean && rm -rf /var/lib/apt/lists/* && apt-get update && apt-get install -y \
#     git \
#     curl \
#     libpng-dev \
#     libjpeg62-turbo-dev \
#     libfreetype-dev \
#     libonig-dev \
#     libxml2-dev \
#     libzip-dev \
#     zip \
#     unzip \
#     default-mysql-client \
#     && docker-php-ext-configure gd --with-freetype --with-jpeg \
#     && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip \
#     && apt-get clean && rm -rf /var/lib/apt/lists/*


RUN apt-get clean && rm -rf /var/lib/apt/lists/* && apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project code
COPY . /var/www/html

# Install Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions for storage and cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Apache configuration
COPY .docker/apache/default.conf /etc/apache2/sites-enabled/000-default.conf

# Expose port 80
EXPOSE 80
