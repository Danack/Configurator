#!/bin/sh -l

set -e

# comment in to debug
# tail -f README.md

php ./composer.phar update

echo '---Installing dependencies---'
php ./composer.phar install

echo '---Running unit tests---'
sh runUnitTests.sh
sh runCodeSniffer.sh

