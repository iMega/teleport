#!/bin/sh

set -e

apk add --update curl

#cd / && curl -0L http://wordpress.org/wordpress-4.2.2.tar.gz | tar zxv
cd / &&
cd /wordpress/wp-content/plugins && wget http://downloads.wordpress.org/plugin/woocommerce.zip; unzip woocommerce.zip;rm woocommerce.zip
/usr/sbin/nginx -g "daemon off;"

exec "$@"
