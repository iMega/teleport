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
namespace iMega\Teleport\Buffers;

use iMega\Teleport\BufferInterface;

class Memcache implements BufferInterface
{
    /**
     * @var \Memcache
     */
    protected $client;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param        $client
     * @param array  $options
     */
    public function __construct($client, array $options = [])
    {
        $this->client  = $client;
        $this->options = $options;
    }

    public function keys()
    {
    }

    /**
     * @param string $key
     *
     * @return array|string
     */
    public function get($key)
    {
        return $this->client->get($key);
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        if (isset($this->options['memcache.timelive'])) {
            $timeLive = $this->options['memcache.timelive'];
        } else {
            $timeLive = 3600;
        }
        $this->client->set($key, $value, $this->options);
    }
}
