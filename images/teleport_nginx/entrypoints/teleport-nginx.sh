#!/bin/sh

set -e

mkdir -p /wordpress/wp-content/uploads
ln -f -s /storage /wordpress/wp-content/uploads/teleport

/usr/sbin/nginx -g "daemon off;"

exec "$@"
