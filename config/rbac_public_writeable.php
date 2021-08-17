<?php

declare(strict_types=1);

use CooarchiApp\Handler;

return [
    'roles' => [
        CooarchiEntities\User::ROLE_ADMINISTRATA => [],
        CooarchiEntities\User::ROLE_KOLLEKTIVISTA => [CooarchiEntities\User::ROLE_ADMINISTRATA],
        CooarchiEntities\User::ROLE_TRAVELLA => [CooarchiEntities\User::ROLE_KOLLEKTIVISTA],
    ],
    'permissions' => [
        CooarchiEntities\User::ROLE_TRAVELLA => [],
        CooarchiEntities\User::ROLE_KOLLEKTIVISTA => [],
        CooarchiEntities\User::ROLE_ADMINISTRATA => [
            Handler\InvitationManagementHandler::ROUTE_NAME,
            Handler\InvitationRemovalHandler::ROUTE_NAME,
            Handler\PingHandler::ROUTE_NAME,
        ],
    ],
];
