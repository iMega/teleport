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

namespace iMega\Teleport\Service;

class AcceptFileService
{
    /**
     * @var \iMega\Teleport\Cloud\ApiCloud $client
     */
    protected $cloud;

    public function __construct($cloud)
    {
        $this->cloud = $cloud;
    }

    /**
     * Загрузить файлы
     *
     * @param array $files Список файлов
     */
    public function downloads(array $files)
    {
        foreach ($files as $item) {
            $ret = $this->download($item['url'], $item['sum']);
            if (false === $ret) {
                throw new \RuntimeException('Неудалось загрузить файл ' . $item['url']);
            }
        }
    }

    /**
     * Загрузить файл
     *
     * @param string $url
     * @param string $hash
     *
     * @return bool
     */
    public function download($url, $hash)
    {
        $file     = basename($url);
        $resource = fopen('teleport://storage/' . $file, 'w+');
        $this->cloud->download($url, ['sink' => $resource]);

        return $hash == md5_file('teleport://storage/' . $file);
    }
}
