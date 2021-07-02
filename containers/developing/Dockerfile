FROM debian:10-slim

USER root

# Get Debian up-to-date
RUN apt-get update -qq \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y git \
    mariadb-client wget curl \
    ca-certificates lsb-release apt-transport-https gnupg unzip

# Install 3rd party PHP packages
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee -a /etc/apt/sources.list.d/php.list

RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg

RUN  apt-get update -qq \
    && DEBIAN_FRONTEND=noninteractive apt-get install --no-install-recommends -y php8.0 php8.0-common php8.0-cli php8.0-fpm \
    php8.0-mysql php8.0-curl php8.0-xml php8.0-mbstring \
    php8.0-intl php8.0-redis php8.0-zip php8.0-xdebug

RUN echo "xdebug.mode=coverage,debug" >> /etc/php/8.0/cli/conf.d/20-xdebug.ini

# Make the default directory be where your code is.
WORKDIR /var/app

CMD tail -f README.md

# CMD sh /var/app/containers/php_fpm/entrypoint.sh