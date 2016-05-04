#!/bin/sh

set -e

apk add --update curl

cd / && curl -0L http://wordpress.org/wordpress-4.2.2.tar.gz | tar zxv
cd /wordpress/wp-content/plugins && curl -o woocommerce.zip -0L http://downloads.wordpress.org/plugin/woocommerce.2.5.5.zip; unzip woocommerce.zip;rm woocommerce.zip
#cd /wordpress/wp-content/themes && curl -o omega.1.2.3.zip -0L https://downloads.wordpress.org/theme/omega.1.2.3.zip; unzip omega.1.2.3.zip;rm omega.1.2.3.zip
#cd /wordpress/wp-content/themes && curl -o shopping.0.4.0.zip -0L https://downloads.wordpress.org/theme/shopping.0.4.0.zip; unzip shopping.0.4.0.zip;rm shopping.0.4.0.zip
mkdir -p /wordpress/wp-content/uploads
ln -s /storage /wordpress/wp-content/uploads/teleport

/usr/sbin/nginx -g "daemon off;"

exec "$@"
