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
use iMega\Teleport\Subscriber\BufferSubscriber;
use iMega\Teleport\Subscriber\PackerSubscriber;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use iMega\Teleport\Subscriber\RequestSubscriber;

/**
 * Class iMegaTeleport
 */
class iMegaTeleport
{
    /**
     * @param CmsInterface $cms Interface cms.
     */
    public function __construct(CmsInterface $cms)
    {
        $options = array_merge_recursive(
            $cms->auth(),
            $cms->db(),
            $cms->storage()
        );
        $appConfig = require_once __DIR__ . '/../../config/app.php';
        $app = new Application(array_merge_recursive($appConfig, $options));

        $app['debug'] = true;
        $app['dispatcher']->addSubscriber(new RequestSubscriber($app));
        $app['dispatcher']->addSubscriber(new BufferSubscriber($app['buffer']));
        $app['dispatcher']->addSubscriber(new PackerSubscriber($app['buffer'], $app['mapper'], 9999999));
        foreach ($cms->subscribers($app) as $subscriber) {
            $app['dispatcher']->addSubscriber($subscriber);
        }

        $app->register(new \Silex\Provider\SecurityServiceProvider());
        $app['security.authentication_provider.dao._proto'] = $app->protect(function ($name) use ($app, $cms) {
            return function () use ($app, $name, $cms) {
                $authProvider = $cms->authProvider();
                return new $authProvider(
                    $app['security.user_provider.'.$name],
                    $app['security.user_checker'],
                    $name,
                    $app['security.encoder_factory'],
                    $app['security.hide_user_not_found']
                );
            };
        });

        $app->register(new \Silex\Provider\ServiceControllerServiceProvider());
        foreach ($app['mount'] as $prefix => $controller) {
            $app->mount($prefix, $controller);
        }
        $request = Request::createFromGlobals();
        if (0 === strpos($request->getBaseUrl(), '/1c_exchange.php')) {
            $app->run();
        }
    }
}
