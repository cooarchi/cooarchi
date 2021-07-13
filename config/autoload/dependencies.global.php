<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Roave\PsrContainerDoctrine\EntityManagerFactory;

return [
    'dependencies' => [
        'aliases' => [
            //'doctrine.entity_manager.orm_default' => EntityManagerInterface::class,
        ],
        'invokables' => [],
        'factories'  => [
            'doctrine.entity_manager.orm_default' => EntityManagerFactory::class,
        ],
    ],
];
