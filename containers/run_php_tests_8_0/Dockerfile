FROM debian:10

USER root

# Get Debian up-to-date
RUN apt-get update -qq \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y git \
    mariadb-client wget curl \
    ca-certificates lsb-release apt-transport-https gnupg bsdmainutils

# Install 3rd party PHP 8.0 packages
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee -a /etc/apt/sources.list.d/php.list
RUN curl -L https://packages.sury.org/php/apt.gpg | apt-key add -

RUN  apt-get update -qq \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y php8.0 php8.0-common php8.0-cli php8.0-fpm \
    php8.0-mysql  php8.0-curl php8.0-xml php8.0-mbstring \
    php8.0-intl php8.0-xdebug php8.0-zip

RUN echo "xdebug.mode=coverage" >> /etc/php/8.0/cli/conf.d/20-xdebug.ini

# Make the default directory you
WORKDIR /var/app

ADD entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]