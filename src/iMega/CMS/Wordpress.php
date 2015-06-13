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

class Wordpress implements CmsInterface
{
    protected $mnemo = 'IMEGATELEPORT';

    public function __construct()
    {

    }

    public function auth()
    {
        return '';
    }

    /**
     * Читает константы указанные в конфиге wp-config,
     * указаные константы IMEGATELEPORT_* имеют больший приоритет над DB_*.
     *
     * @return array
     */
    public function db()
    {
        $config['db.options'] = [];

        $keys = array('user', 'password', 'host', 'port', 'socket');
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

        return $config['db.options'];
    }

    /**
     * Возвращает путь для хранения файлов
     *
     * @return array
     */
    public function storage()
    {
        return [
            'storage.path' => wp_upload_dir(),
        ];
    }

    protected function getMnemoConst($key)
    {
        return strtoupper($this->mnemo.'_'.$key);
    }
}
