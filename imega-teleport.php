<?php
/**
 * Plugin Name: iMega Teleport
 * Plugin URI: http://teleport.imega.club
 * Description: EN:Import your products from your 1C to your new WooCommerce store. RU:Обеспечивает взаимосвязь интернет-магазина и 1С.
 * Version: 2.1.0
 * Author: iMega ltd
 * Author URI: http://imega.ru
 * Requires at least: 3.5
 * Tested up to: 4.2.2
 */
/**
 * Copyright (C) 2014 iMega ltd Dmitry Gavriloff (email: info@imega.ru),
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('ABSPATH')) {
    exit();
}

if (!function_exists('wp_authenticate')) {
    include(ABSPATH . 'wp-includes/pluggable.php');
}
if (!function_exists('get_plugins')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

require_once __DIR__ . '/vendor/autoload.php';

$plugins = get_plugins();

if (array_key_exists('imega-teleport/imega-teleport.php', $plugins)) {
    $imegaTeleport = new \iMega\iMegaTeleport(new iMega\CMS\Wordpress(['pluginPath' => __DIR__]));
}
