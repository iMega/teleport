<?php
use Silex\Application;
use Silex\ControllerResolver;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;

$configApp = __DIR__ . '/../config/app.php';
$options   = __DIR__ . '/../config/options.php';

if (! file_exists($configApp)) {
    throw new FileNotFoundException($configApp);
}

if (! file_exists($options)) {
    throw new FileNotFoundException($options);
}

$configApp = require_once __DIR__ . '/../config/app.php';
$options   = require_once __DIR__ . '/../config/options.php';

$app = new Application(array_merge_recursive($configApp, $options));

$app['debug'] = true;

$app['resolver'] = function () use ($app) {
    return new ControllerResolver($app, $app['logger']);
};

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

foreach ($app['mount'] as $prefix => $controller) {
    $app->mount($prefix, $controller);
}
//$app->register(new \Teleport\Provider\RouteProvider());
$app['dispatcher']->addSubscriber(new \Teleport\Subscriber\RequestSubscriber($app));

return $app;
