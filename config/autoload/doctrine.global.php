<?php

declare(strict_types=1);

use Doctrine\DBAL\Driver\Mysqli\Driver;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;

return [
    'doctrine' => [
        'connection' => [
            'driver_class' => Driver::class,
            'orm_default' => [
                'params' => [
                    'url' => 'mysql://user:password@localhost/database',
                    'charset' => 'utf8mb4',
                    'defaultTableOptions' => [
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_unicode_ci',
                    ],
                ],
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'auto_generate_proxy_classes' => false,
                'proxy_dir' => __DIR__ . '/../../data/cache/DoctrineEntityProxy',
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class' => MappingDriverChain::class,
                'drivers' => [
                    'CooarchiEntities' => 'default_driver',
                ],
            ],
            'default_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../../src/Entities/src'],
            ],
        ],
        'types' => [
            UuidBinaryOrderedTimeType::NAME => UuidBinaryOrderedTimeType::class,
        ],
    ],
];
