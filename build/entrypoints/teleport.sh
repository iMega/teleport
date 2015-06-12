#!/bin/sh

set -e

ln -s /app /wordpress/wp-content/plugins/imega-teleport
cp /app/build/wordpress/wp-config.php /wordpress/wp-config.php

exec "$@"
