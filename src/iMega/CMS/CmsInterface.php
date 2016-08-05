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

use Silex\Application;
use iMega\Teleport\MapperInterface;

interface CmsInterface
{
    /**
     * @return string
     */
    public function authProvider();

    /**
     * @return array
     */
    public function auth();

    /**
     * @return array
     */
    public function db();

    /**
     * @return array
     */
    public function storage();

    /**
     * @return array
     */
    public function attaches();

    /**
     * @param Application $app Приложение.
     *
     * @return array
     */
    public function subscribers(Application $app);

    /**
     * @return string
     */
    public function getLogin();

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return bool
     */
    public function isRegistered();

    /**
     * @param mixed $response
     */
    public function setRegistered($response);

    /**
     * Чтение/Запись значения прогресса выполнения задачи
     *
     * @param null|int $value
     *
     * @return int
     */
    public function progress($value = null);

    /**
     * Импорт дампа
     *
     * @param MapperInterface $mapper
     * @param string          $file
     *
     * @return bool
     */
    public function import($mapper, $file);
}
