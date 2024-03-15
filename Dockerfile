FROM wordpress:latest

RUN pecl install xdebug

COPY config/php /usr/local/etc/php

RUN apt-get update && apt-get install -y less