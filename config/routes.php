<?php

declare(strict_types=1);

use CooarchiApp\Handler;
use CooarchiApp\Middleware;
use Mezzio\Application;
use Mezzio\Helper\BodyParams\BodyParamsMiddleware;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

/**
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomeHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/:id', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/:id', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Mezzio\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */
return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void
{
    // Ping
    $app->get(
        Handler\PingHandler::ROUTE,
        Handler\PingHandler::class,
        Handler\PingHandler::ROUTE_NAME
    );

    // Home
    $app->get(
        Handler\HomeHandler::ROUTE,
        [
            Middleware\AuthMiddleware::class,
            Middleware\SlimFlashMiddleware::class,
            Middleware\TemplateVariablesMiddleware::class,
            Handler\HomeHandler::class,
        ],
        Handler\HomeHandler::ROUTE_NAME
    );

    // Help
    $app->get(
        Handler\HelpHandler::ROUTE,
        [
            Middleware\TemplateVariablesMiddleware::class,
            Handler\HelpHandler::class,
        ],
        Handler\HelpHandler::ROUTE_NAME
    );

    // Invitation Management
    $app->route(
        Handler\InvitationManagementHandler::ROUTE,
        [
            Middleware\AuthMiddleware::class,
            Middleware\PermissionMiddleware::class,
            Middleware\SlimFlashMiddleware::class,
            Middleware\TemplateVariablesMiddleware::class,
            Handler\InvitationManagementHandler::class,
        ],
        ['GET', 'POST'],
        Handler\InvitationManagementHandler::ROUTE_NAME
    );
    // Invitation Removal
    $app->get(
        Handler\InvitationRemovalHandler::ROUTE,
        [
            Middleware\AuthMiddleware::class,
            Middleware\PermissionMiddleware::class,
            Middleware\SlimFlashMiddleware::class,
            Middleware\TemplateVariablesMiddleware::class,
            Handler\InvitationRemovalHandler::class,
        ],
        Handler\InvitationRemovalHandler::ROUTE_NAME
    );

    // User Management
    $app->route(
        Handler\UserManagementHandler::ROUTE,
        [
            Middleware\AuthMiddleware::class,
            Middleware\PermissionMiddleware::class,
            Middleware\SlimFlashMiddleware::class,
            Middleware\TemplateVariablesMiddleware::class,
            Handler\UserManagementHandler::class,
        ],
        ['GET', 'POST'],
        Handler\UserManagementHandler::ROUTE_NAME
    );
    // User Removal
    $app->get(
        Handler\UserRemovalHandler::ROUTE,
        [
            Middleware\AuthMiddleware::class,
            Middleware\PermissionMiddleware::class,
            Middleware\SlimFlashMiddleware::class,
            Middleware\TemplateVariablesMiddleware::class,
            Handler\UserRemovalHandler::class,
        ],
        Handler\UserRemovalHandler::ROUTE_NAME
    );

    // Content Management
    $app->route(
        Handler\ContentManagementHandler::ROUTE,
        [
            Middleware\AuthMiddleware::class,
            Middleware\PermissionMiddleware::class,
            Middleware\SlimFlashMiddleware::class,
            Middleware\TemplateVariablesMiddleware::class,
            Handler\ContentManagementHandler::class,
        ],
        ['GET', 'POST'],
        Handler\ContentManagementHandler::ROUTE_NAME
    );
    // Content Removal
    $app->get(
        Handler\ContentRemovalHandler::ROUTE,
        [
            Middleware\AuthMiddleware::class,
            Middleware\PermissionMiddleware::class,
            Middleware\SlimFlashMiddleware::class,
            Middleware\TemplateVariablesMiddleware::class,
            Handler\ContentRemovalHandler::class,
        ],
        Handler\ContentRemovalHandler::ROUTE_NAME
    );

    // File Management
    $app->route(
        Handler\FileManagementHandler::ROUTE,
        [
            Middleware\AuthMiddleware::class,
            Middleware\PermissionMiddleware::class,
            Middleware\SlimFlashMiddleware::class,
            Middleware\TemplateVariablesMiddleware::class,
            Handler\FileManagementHandler::class,
        ],
        ['GET', 'POST'],
        Handler\FileManagementHandler::ROUTE_NAME
    );
    // File Removal
    $app->get(
        Handler\FileRemovalHandler::ROUTE,
        [
            Middleware\AuthMiddleware::class,
            Middleware\PermissionMiddleware::class,
            Middleware\SlimFlashMiddleware::class,
            Middleware\TemplateVariablesMiddleware::class,
            Handler\FileRemovalHandler::class,
        ],
        Handler\FileRemovalHandler::ROUTE_NAME
    );

    // Registration
    $app->route(
        Handler\RegistrationHandler::ROUTE,
        [
            Middleware\AuthMiddleware::class,
            Middleware\SlimFlashMiddleware::class,
            Middleware\TemplateVariablesMiddleware::class,
            Handler\RegistrationHandler::class,
        ],
        ['GET', 'POST'],
        Handler\RegistrationHandler::ROUTE_NAME
    );

    // Login & Logout
    $app->route(
        Handler\LoginHandler::ROUTE,
        [
            Middleware\TemplateVariablesMiddleware::class,
            Handler\LoginHandler::class,
        ],
        ['GET', 'POST'],
        Handler\LoginHandler::ROUTE_NAME
    );
    $app->get(
        Handler\LogoutHandler::ROUTE,
        [
            Middleware\TemplateVariablesMiddleware::class,
            Handler\LogoutHandler::class,
        ],
        Handler\LogoutHandler::ROUTE_NAME
    );

    // API Data endpoint
    $app->get(
        Handler\GetDataHandler::ROUTE,
        [
            Middleware\AuthMiddleware::class,
            Middleware\PermissionMiddleware::class,
            Handler\GetDataHandler::class,
        ],
        Handler\GetDataHandler::ROUTE_NAME
    );

    // API Save Endpoint
    $app->post(
        Handler\SaveHandler::ROUTE,
        [
            Middleware\AuthMiddleware::class,
            Middleware\PermissionMiddleware::class,
            BodyParamsMiddleware::class,
            Handler\SaveHandler::class,
        ],
        Handler\SaveHandler::ROUTE_NAME
    );

    // API Upload Endpoint
    $app->post(
        Handler\UploadHandler::ROUTE,
        [
            Middleware\AuthMiddleware::class,
            Middleware\PermissionMiddleware::class,
            Handler\UploadHandler::class,
        ],
        Handler\UploadHandler::ROUTE_NAME
    );

    // API Settings Endpoint
    $app->get(
        Handler\SettingsHandler::ROUTE,
        Handler\SettingsHandler::class,
        Handler\SettingsHandler::ROUTE_NAME
    );

    // API Auth Status Endpoint
    $app->get(
        Handler\AuthStatusHandler::ROUTE,
        [
            Middleware\AuthMiddleware::class,
            Handler\AuthStatusHandler::class,
        ],
        Handler\AuthStatusHandler::ROUTE_NAME
    );
};
