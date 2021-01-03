FROM php:7.4-cli

RUN apt-get update -y && apt-get install -y libmcrypt-dev libonig-dev git curl wget zip unzip vim libmagickwand-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev  --no-install-recommends


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install imagick && docker-php-ext-enable imagick 

RUN docker-php-ext-install pdo mbstring pdo_mysql mysqli gd json  

RUN apt-get install  -y tmux && tmux new -s cron_message -d && tmux new -s cron_queue -d && tmux send -t cron_message sh /app/runer_messages.sh ENTER && tmux send -t cron_message sh /app/runer_queue.sh ENTER

COPY ./ImageMagick6-policy.xml /etc/ImageMagick-6/policy.xml
WORKDIR /app
COPY . /app

# RUN composer install

EXPOSE 8000
CMD  cd public_html && php -S 0.0.0.0:8000