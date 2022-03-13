FROM lscr.io/linuxserver/dokuwiki:latest
LABEL Name=fake-dokuwiki Version=0.0.1

ARG FAKE_DW_PAGES=100
ARG FAKE_DW_USERS=10
ARG FAKE_DW_NAMESPACES=3

ENV FAKE_DW_PAGES=$FAKE_DW_PAGES
ENV FAKE_DW_USERS=$FAKE_DW_USERS
ENV FAKE_DW_NAMESPACES=$FAKE_DW_NAMESPACES

WORKDIR /opt/

RUN apk add php-phar && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    php composer.phar require fakerphp/faker

COPY ./gen-fake-wiki.php /app/dokuwiki/bin/gen-fake-wiki.php

WORKDIR /app/dokuwiki/bin

RUN php ./gen-fake-wiki.php
