#!/usr/bin/env bash

php vendor/bin/phpcs --standard=./test/codesniffer.xml -s --encoding=utf-8 ./src
