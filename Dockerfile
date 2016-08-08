FROM harningt/base-alpine-s6-overlay:3.2
MAINTAINER info@analogic.cz

RUN apk --update add php php-cli php-fpm php-curl php-openssl php-phar php-json php-ctype nginx && \
    rm -rf /var/cache/apk/* && sed -i 's/nobody/root/g' /etc/php/php-fpm.conf

ADD bin /opt/lemanager/bin
ADD src /opt/lemanager/src
ADD web /opt/lemanager/web
COPY composer.* /opt/lemanager/

ADD https://getcomposer.org/installer /opt/lemanager/composer-setup.php
RUN cd /opt/lemanager && php composer-setup.php && rm composer-setup.php && php composer.phar install

ADD rootfs /
EXPOSE 80
