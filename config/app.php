<?php

use Silex\Application;

return [
    'user.agents' => array(
        '1C+Enterprise' => '1c_enterprise',
        'Java'          => 'java',
    ),
    'mount' => [
        '/default' => new \iMega\Teleport\MainController(),
    ],
    'storage.adapter' => function (Application $app) {
        return new Gaufrette\Adapter\Local($app['storage.path'], true);
    },
    'storage' => function (Application $app) {
        $fs = new Gaufrette\Filesystem($app['storage.adapter']);
        $filesystemMap = Gaufrette\StreamWrapper::getFilesystemMap();
        $filesystemMap->set('teleport', $fs);
        Gaufrette\StreamWrapper::register();
        return $fs;
    },
    'resources' => function () {
        $adapter = new Gaufrette\Adapter\Local(__DIR__ . '/../resources');
        return new Gaufrette\Filesystem($adapter);
    },
    'buffer' => function () {
        return new \iMega\Teleport\Buffers\Memory();
    },
    'mapper' => function (Application $app) {
        return new iMega\Teleport\Mapper\Mysqlnd($app['db.options']);
    }
];
