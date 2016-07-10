<?php

namespace iMega\Teleport\Provider;

use iMega\Teleport\Service\CurlService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;

/**
 * Class CurlServiceProvider
 */
class CurlServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     *
     * @return void
     */
    public function register(Container $container)
    {
        $container['curl'] = function (Application $app) {
            $guzzleClient = null;
            $logger = null;
            if ($app->offsetExists('guzzle.client')) {
                $guzzleClient = $app['guzzle.client'];
            }
            if ($app->offsetExists('logger')) {
                $logger = $app['logger'];
            }

            return new CurlService($app['curl.options'], $logger, $guzzleClient);
        };
    }
}
