FROM php:7.4-cli

RUN apt-get update -y && apt-get install -y libmcrypt-dev libonig-dev git curl wget zip unzip vim 


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo mbstring pdo_mysql mysqli 


WORKDIR /app
COPY . /app

# RUN composer install

EXPOSE 8000
CMD php -S 0.0.0.0:8000