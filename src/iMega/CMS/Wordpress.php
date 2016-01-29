<?php
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
namespace iMega\CMS;

use iMega\CMS\Security\User\Provider\WordpressUserProvider;
use iMega\CMS\Subscriber\WordpressSubscriber;
use Silex\Application;

/**
 * Class Wordpress
 */
class Wordpress implements CmsInterface
{
    protected $mnemo = 'IMEGATELEPORT';

    /**
     * Construct. trailing slash.
     */
    public function __construct()
    {
        if (array_key_exists('DOCUMENT_URI', $_SERVER) &&
            !empty($_SERVER['DOCUMENT_URI']) &&
            $_SERVER['DOCUMENT_URI'] == '/1c_exchange.php'
        ) {
            $_SERVER['REQUEST_URI'] = $_SERVER['DOCUMENT_URI'] . '/?' . $_SERVER['QUERY_STRING'];
            $_SERVER['SCRIPT_NAME'] = '/1c_exchange.php/index.php';
        }
    }

    /**
     * @return string
     */
    public function authProvider()
    {
        return '\iMega\CMS\Authentication\Provider\WordpressAuthProvider';
    }

    /**
     * Return security
     *
     * @return array
     */
    public function auth()
    {
        return [
            'security.firewalls' => array(
                'http-auth' => array(
                    'pattern' => '^.*$',
                    'http' => true,
                    'users' => function () {
                        return new WordpressUserProvider();
                    },
                ),
            ),
        ];
    }

    /**
     * Читает константы указанные в конфиге wp-config,
     * указаные константы IMEGATELEPORT_* имеют больший приоритет над DB_*.
     *
     * @return array
     */
    public function db()
    {
        $config = [];

        $keys = array('name', 'user', 'password', 'host', 'port', 'socket', 'prefix');
        foreach ($keys as $key) {
            $value = null;
            $constName = strtoupper('DB_'.$key);
            if (null !== @constant($constName)) {
                $value = constant($constName);
            }

            if (null === @constant($this->getMnemoConst($key))) {
                $config['db.options'][$key] = $value;
            } else {
                $config['db.options'][$key] = constant($this->getMnemoConst($key));
            }
        }

        return $config;
    }

    /**
     * Возвращает url до файлов товаров
     *
     * @return array
     */
    public function attaches()
    {
        $uploadDir = wp_upload_dir();
        return [
            'attaches.path' => $uploadDir['baseurl'] . '/teleport',
        ];
    }

    /**
     * Возвращает путь для хранения файлов
     *
     * @return array
     */
    public function storage()
    {
        $uploadDir = wp_upload_dir();
        return [
            'storage.path' => $uploadDir['basedir'] . '/teleport',
        ];
    }

    /**
     * Возвращает подписчиков событий
     *
     * @param Application $app Приложение.
     *
     * @return array
     */
    public function subscribers(Application $app)
    {
        return [
            new WordpressSubscriber($app['mapper'], $app['resources'], $this->getPatterns($app)),
        ];
    }

    protected function getMnemoConst($key)
    {
        return strtoupper($this->mnemo.'_'.$key);
    }

    protected function getPatterns(Application $app)
    {
        return [
            '{$table_prefix}' => $app['db.options']['prefix'],
            '{$baseurl}'      => $app['attaches.path'] . '/',
            '{$imgpath}'      => '',
        ];
    }
}
