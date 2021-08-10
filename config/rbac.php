<?php

declare(strict_types=1);

use CooarchiApp\Handler;

return [
    'roles' => [
        CooarchiEntities\User::ROLE_ADMINISTRATA => [],
        CooarchiEntities\User::ROLE_KOLLEKTIVISTA => [CooarchiEntities\User::ROLE_ADMINISTRATA],
    ],
    'permissions' => [
        CooarchiEntities\User::ROLE_KOLLEKTIVISTA => [
            Handler\GetDataHandler::ROUTE,
            Handler\SaveHandler::ROUTE,
            Handler\UploadHandler::ROUTE,
        ],
        CooarchiEntities\User::ROLE_ADMINISTRATA => [
            Handler\PingHandler::ROUTE,
        ],
    ],
];
