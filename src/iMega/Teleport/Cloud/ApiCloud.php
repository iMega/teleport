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
namespace iMega\Teleport\Cloud;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client as GuzzleClient;

class ApiCloud
{
    protected $client;

    public function __construct(array $options = [], ClientInterface $client = null)
    {
        $options = array_replace(array(
            'base_uri' => 'http://localhost',
            'debug' => false,
        ), $options);

        $this->client = $client ?: new GuzzleClient($options);
    }

    public function registered($login, $url)
    {
        $data = [
            'url' => $url,
        ];
        //$response = $this->client->post('/activate/register-plugin/' . $login, ['json' => $data]);
        //var_dump($response->getBody()->__toString());
    }
}
