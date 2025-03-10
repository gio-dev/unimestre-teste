FROM php:8.2-fpm

# Instalar dependências
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql opcache


# Definir diretório de trabalho
WORKDIR /var/www

# Expor a porta
EXPOSE 9000

CMD ["php-fpm"]
