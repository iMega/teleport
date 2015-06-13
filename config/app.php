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
];

