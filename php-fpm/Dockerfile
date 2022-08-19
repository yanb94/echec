FROM php:8.0-fpm

# Install packages
RUN apt-get update && apt-get install -y \
    openssl \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    libmagickwand-dev \
    inkscape

# Install PHP extensions
RUN docker-php-ext-configure \
    intl

RUN docker-php-ext-install -j$(nproc) \
    intl \
    pdo \
    pdo_mysql \
    opcache \
    zip

#Install Imagick
RUN mkdir -p /usr/src/php/ext/imagick; \
    curl -fsSL https://github.com/Imagick/imagick/archive/06116aa24b76edaf6b1693198f79e6c295eda8a9.tar.gz | tar xvz -C "/usr/src/php/ext/imagick" --strip 1; \
    docker-php-ext-install imagick;

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Install NodeJs
RUN apt-get install -y curl \
  && curl -sL https://deb.nodesource.com/setup_15.x | bash - \
  && apt-get install -y nodejs \
  && curl -L https://www.npmjs.com/install.sh | sh

# Install yarn
RUN npm install --global yarn

# Add php Configuration
COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY ./memory_limit.ini /usr/local/etc/php/conf.d/memory_limit.ini

RUN useradd yanb -m
USER yanb

RUN git config --global user.email "20875343+yanb94@users.noreply.github.com" \
  && git config --global user.name "Yann-Elias BELLAGNECH"

WORKDIR /var/www/html