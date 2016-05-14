FROM leanlabs/php:1.1.1

VOLUME ["/app", "/wordpress"]

RUN echo "@stale http://dl-4.alpinelinux.org/alpine/edge/testing" >> /etc/apk/repositories && \
    apk add --update \
        php-zlib \
        php-mysqli \
        php-xdebug@stale && \
    rm -rf /var/cache/apk/*

ENTRYPOINT ["/bin/sh", "/app/images/teleport/entrypoints/teleport.sh"]

CMD ["php-fpm", "-F", "-d error_reporting=E_ALL", "-d log_errors=ON", "-d error_log=/dev/stdout","-d display_errors=YES"]
