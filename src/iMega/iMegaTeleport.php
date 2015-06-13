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
namespace iMega;

use iMega\CMS\CmsInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use iMega\Teleport\Subscriber\RequestSubscriber;

/**
 * Class iMegaTeleport
 */
class iMegaTeleport
{
    /**
     * @param CmsInterface $cms
     */
    public function __construct(CmsInterface $cms)
    {
        $options = array_merge_recursive(
            $cms->db()
        );
        $appConfig = require_once __DIR__ . '/../../config/app.php';
        $app = new Application(array_merge_recursive($appConfig, $options));

        $app['debug'] = true;
        $app['dispatcher']->addSubscriber(new RequestSubscriber($app));
        $app->register(new \Silex\Provider\ServiceControllerServiceProvider());
        foreach ($app['mount'] as $prefix => $controller) {
            $app->mount($prefix, $controller);
        }
        $request = Request::createFromGlobals();
        $response = $app->handle($request);

        if (0 === strpos($request->getBaseUrl(), '/1c_exchange.php')) {
            $response->send();
        }
        $app->terminate($request, $response);
    }
}
