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

namespace iMega\Teleport\Provider;

use iMega\Teleport\Cloud\ApiCloud;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class TeleportCloudServiceProvider
 */
class TeleportCloudServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $app
     *
     * @return void
     */
    public function register(Container $app)
    {
        $app['teleport.cloud'] = function ($app) {
            return new ApiCloud($app['teleport.cloud.options']);
        };
    }
}
