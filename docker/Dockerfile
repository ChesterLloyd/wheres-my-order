FROM node:22 AS node

WORKDIR /app
COPY assets ./assets
COPY public ./public
COPY package*.json webpack.config.js ./
RUN npm ci
RUN npm run build


FROM php:8.4-apache-bookworm

ARG ENV=prod

RUN apt-get update \
  && apt-get upgrade -y \
  && apt-get install -y libicu-dev libpng-dev libzip-dev libsodium-dev git nano unzip \
  && docker-php-ext-install gd mysqli zip pdo pdo_mysql intl sodium \
  && apt-get clean

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin

# Setup Apache
COPY /docker/apache2-prod.conf /etc/apache2/sites-available/wheresmyorder.conf
COPY /docker/apache2-local.conf /etc/apache2/sites-available/wheresmyorder-local.conf
RUN a2enmod headers \
  && if [ "${ENV}" = "prod" ]; then a2ensite wheresmyorder.conf; else a2ensite wheresmyorder-local.conf; fi

RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf \
  && echo "ServerSignature Off" >> /etc/apache2/apache2.conf \
  && sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf

# Copy website files
COPY . /var/www/html
WORKDIR /var/www/html

RUN chown 33:33 -R /var/www/html
USER 33

RUN mkdir -p public/build
COPY --from=node /app/public/build /var/www/html/public/build

RUN composer install --optimize-autoloader --no-scripts
RUN rm -rf /var/www/html/var/cache/*

# Clean up tasks
RUN rm -r docker  \
  && touch .env

EXPOSE 8080
COPY docker/entrypoint.sh /var/www/entrypoint.sh
ENTRYPOINT ["bash", "/var/www/entrypoint.sh"]
