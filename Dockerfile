FROM php:8.1-cli
RUN apt-get update && apt-get install -y git unzip libzip-dev libpng-dev libonig-dev libxml2-dev default-mysql-client && docker-php-ext-install pdo_mysql mbstring xml zip && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer
WORKDIR /workspace
CMD ["./start-dev.sh"]
