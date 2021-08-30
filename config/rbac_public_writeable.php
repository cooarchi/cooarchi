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
        CooarchiEntities\User::ROLE_TRAVELLA => [
            Handler\GetDataHandler::ROUTE_NAME,
            Handler\SaveHandler::ROUTE_NAME,
            Handler\UploadHandler::ROUTE_NAME,
        ],
        CooarchiEntities\User::ROLE_KOLLEKTIVISTA => [],
        CooarchiEntities\User::ROLE_ADMINISTRATA => [
            Handler\ContentManagementHandler::ROUTE_NAME,
            Handler\ContentRemovalHandler::ROUTE_NAME,
            Handler\FileManagementHandler::ROUTE_NAME,
            Handler\FileRemovalHandler::ROUTE_NAME,
            Handler\InvitationManagementHandler::ROUTE_NAME,
            Handler\InvitationRemovalHandler::ROUTE_NAME,
            Handler\PingHandler::ROUTE_NAME,
            Handler\UserManagementHandler::ROUTE_NAME,
            Handler\UserRemovalHandler::ROUTE_NAME,
        ],
    ],
];
