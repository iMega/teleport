<?php

use Silex\Application;

return array(
    'user.agents' => array(
        '1C+Enterprise' => '1c_enterprise',
        'Java'          => 'java',
    ),
    'mount' => array(
        '/default' => new \Teleport\Controller\Teleport(),
        '/1c_enterprise' => new \Teleport\Controller\OneCEnterprise(),
        '/java' => new \Teleport\Controller\Java(),
    ),
    'controller.default' => function () {
        return new \Teleport\Controller\Teleport();
    },
    'controller.1c+enterprise' => function () {
        return new \Teleport\Controller\OneCEnterprise();
    },
    'controller.java' => function () {
        return new \Teleport\Controller\Java();
    },
);
