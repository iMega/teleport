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
namespace iMega\Teleport;


interface StorageInterface
{
    /**
     * Deletes the file matching the specified key
     *
     * @param  string $resource
     *
     * @return bool
     */
    public function delete($resource);

    /**
     * Reads the content from the file
     *
     * @param string $resource
     *
     * @return string
     */
    public function read($resource);

    /**
     * Writes the given content into the file
     *
     * @param $resource
     * @param $data
     * @param $overwrite
     *
     * @return integer The number of bytes that were written into the file
     */
    public function write($resource, $data, $overwrite);
}
