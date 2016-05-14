#!/bin/sh

set -e

ln -f -s /app /wordpress/wp-content/plugins/imega-teleport
mkdir -p /wordpress/wp-content/uploads
ln -f -s /storage /wordpress/wp-content/uploads/teleport
cp /app/images/teleport/wordpress/wp-config.php /wordpress/wp-config.php
#ln -s /app/modules/xdebug.so /usr/lib/php/modules/xdebug.so
sed -i 's/memory_limit = .*/memory_limit = 512M/' /etc/php/php.ini
echo 'extension=xdebug.so' >> /etc/php/conf.d/xdebug.ini
echo -e "[xdebug]\n
    xdebug.default_enable = On;\n
    xdebug.var_display_max_depth = 6;\n
    xdebug.remote_enable = On;\n
    xdebug.remote_host = 10.0.3.2;\n
    xdebug.remote_port = 9005;\n
    ;xdebug.remote_handler = dbgp;\n
    ;xdebug.idekey = netbeans-xdebug;\n
    xdebug.remote_autostart = 1;\n
    ;xdebug.remote_log=/tmp/xdebug/xdebug.log;\n
    xdebug.profiler_enable_trigger = 1;\n
    xdebug.profiler_enable = 0;\n
    xdebug.profiler_output_dir = /tmp/xdebug/profiler/;\n
    xdebug.show_local_vars = 1;\n
    xdebug.overload_var_dump = 1;\n" >> /etc/php/php.ini

exec "$@"
