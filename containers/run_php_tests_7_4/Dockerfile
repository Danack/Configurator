FROM debian:10

USER root

# Get Debian up-to-date
RUN apt-get update -qq \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y git \
    mariadb-client wget curl \
    ca-certificates lsb-release apt-transport-https gnupg bsdmainutils

# Install 3rd party PHP 7.4 packages
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee -a /etc/apt/sources.list.d/php.list
RUN curl -L https://packages.sury.org/php/apt.gpg | apt-key add -

RUN  apt-get update -qq \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y php7.4 php7.4-common php7.4-cli php7.4-fpm \
    php7.4-mysql  php7.4-curl php7.4-xml php7.4-mbstring \
    php7.4-intl php7.4-xdebug php7.4-zip

RUN echo "xdebug.mode=coverage" >> /etc/php/7.4/cli/conf.d/20-xdebug.ini

# Make the default directory you
WORKDIR /var/app

ADD entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]