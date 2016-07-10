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
    protected $pluginPath;
    protected $mnemo = 'IMEGATELEPORT';
    protected $name  = 'iMega Teleport';
    protected $login = null;

    /**
     * Construct. trailing slash.
     */
    public function __construct(array $options)
    {
        $this->pluginPath = $options['pluginPath'];

        if ($this->login = $this->getUser()) {
            update_option('imegateleport-login', $this->login);
        };

        if ($this->getExpiredDate()) {
            //update_option('imegateleport-expired', $expire);
        }

        $this->hooks();
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
            new WordpressSubscriber($app['mapper'], $app['storage'], $this->getPatterns($app)),
        ];
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->getUser();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return get_option('siteurl');
    }

    public function isRegistered()
    {
        return false;
    }

    public function setRegistered($response)
    {
        //update_option('imegateleport-registered', $);
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

    /**
     * Action Hooks
     */
    function hooks()
    {
        add_action(
            'admin_menu',
            array(
                $this,
                'pluginMenuSettings'
            )
        );
    }

    /**
     * Пунтк меню с настройками
     */
    function pluginMenuSettings()
    {
        add_options_page(
            __('Settings') . ' ' . $this->name,
            $this->name,
            'manage_options',
            $this->mnemo . '_settings',
            array(
                $this,
                'pluginPageSettings'
            )
        );
    }

    /**
     * Страница с настройками
     */
    function pluginPageSettings()
    {
        $text = $this->loadFile('resources/settings-form.htm');
        $text = str_replace('{$title}', __('Settings') . ' ' . $this->name,
            $text);
        $text = str_replace('{$logo}',
            plugins_url('/teleport.png', __FILE__), $text);
        /*$text = str_replace('{$path}',
            $this->path('basedir') . $this->path($this->mnemo), $text);*/
        $text = str_replace('{$url}', get_site_url(), $text);
        /*$text = str_replace('{$stat}', $this->stat(), $text);*/
        $text = str_replace('{$feedback}', __('Feedback'), $text);

        $checked = '';
        $name = get_option('imegateleport-settings-fullname');
        if ($name == 'true')
            $checked = ' checked value=1';
        $text = str_replace('{$checked_name}', $checked, $text);

        $checked = '';
        $name = get_option('imegateleport-settings-kod');
        if ($name == 'true')
            $checked = ' checked value=1';
        $text = str_replace('{$checked_kod}', $checked, $text);

        $checked = '';
        $zip = get_option('imegateleport-settings-zip');
        if ($zip == 'true')
            $checked = ' checked value=1';
        $text = str_replace('{$checked_zip}', $checked, $text);

        $checked = '';
        $article = get_option('imegateleport-settings-article');
        if ($article == 'true')
            $checked = ' checked value=1';
        $text = str_replace('{$checked_article}', $checked, $text);

        $checked = '';
        $fulldesc = get_option('imegateleport-settings-fulldesc');
        if ($fulldesc == 'true')
            $checked = ' checked value=1';
        $text = str_replace('{$checked_fulldesc}', $checked, $text);

        $opt = get_option('imegateleport-settings-warehouse');
        $displayWarehouses = 'none';
        if (!empty($opt)) {
            $displayWarehouses = 'block';
            $warehousesActive = '';//$this->getWarehouseActive();
            $warehouses = json_decode($opt);
            $stockData = '';
            $i = 0;
            foreach ($warehouses as $key => $value) {
                $stockData .= $this->getInputCheck($i, $value, array_key_exists($key, $warehousesActive));
                $i++;
            }
            $text = str_replace('{$warehouses}', $stockData, $text);
        }
        $text = str_replace('{$display_warehouses}', $displayWarehouses, $text);

        $login = get_option('imegateleport-login');
        if (false === $login) {
            $text = str_replace('{$login}', '<a href="/wp-admin/user-new.php">Создайте нового пользователя</a>', $text);
        } else {
            $text = str_replace('{$login}', $login, $text);
        }


        echo $text;
    }

    /**
     * Загружает файл с текущей директори плагина
     *
     * @param string $filename
     * @return string
     */
    function loadFile($filename, $force = false)
    {
        $dir = $this->pluginPath;
        $text = file_get_contents("{$dir}/{$filename}");
        //$text = str_replace('{$table_prefix}', $this->table_prefix, $text);
        return $text;
    }

    /**
     * Возвращает логин пользователя для телепорта
     *
     * @return string
     */
    protected function getUser()
    {
        if (null !== $this->login) {
            return $this->login;
        }
        if (false === $login = get_option('imegateleport-login')) {
            $users = get_users('role=administrator&orderby=registered&order=desc');
            foreach ($users as $user) {
                $res = preg_match('/[a-fA-F0-9\-]{36}/', $user->user_login);
                if (false !== $res && 1 == $res) {
                    $login = $user->user_login;
                    break;
                }
            }
        }

        return $login;
    }

    protected function getExpiredDate()
    {
        return get_option('imegateleport-expired');
    }
}
