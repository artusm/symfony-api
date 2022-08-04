FROM php:8.1-cli

ENV PHP_IDE_CONFIG="serverName=dockerHost"
ENV LOG_DIR="/app/var/log"

RUN echo 'deb [trusted=yes] https://repo.symfony.com/apt/ /' | tee /etc/apt/sources.list.d/symfony-cli.list

RUN apt-get update && apt-get install -y \
    libonig-dev \
    libmcrypt-dev \
    libpq-dev \
    librabbitmq-dev \
    libzip-dev \
    unzip \
    git \
    symfony-cli

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    zip

RUN docker-php-source extract && \
    mkdir /usr/src/php/ext/amqp && \
    curl -L https://github.com/php-amqp/php-amqp/archive/master.tar.gz | tar -xzC /usr/src/php/ext/amqp --strip-components=1 && \
    docker-php-ext-install amqp && \
    docker-php-source delete

RUN pecl install xdebug-3.1.5
RUN docker-php-ext-enable xdebug

WORKDIR /app
COPY . /app

RUN composer install --no-scripts
# RUN symfony server:ca:install

EXPOSE 8000
CMD symfony server:start --port=8000