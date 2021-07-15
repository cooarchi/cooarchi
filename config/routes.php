<?php
declare(strict_types=1);

use CooarchiApp\Handler;
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
return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    $app->get(Handler\HomeHandler::ROUTE, Handler\HomeHandler::class, Handler\HomeHandler::ROUTE_NAME);
    $app->get(
        Handler\GetDataHandler::ROUTE,
        Handler\GetDataHandler::class,
        Handler\GetDataHandler::ROUTE_NAME
    );
    $app->get(Handler\PingHandler::ROUTE, Handler\PingHandler::class, Handler\PingHandler::ROUTE_NAME);
    $app->post(
        Handler\SaveHandler::ROUTE,
        [
            BodyParamsMiddleware::class,
            Handler\SaveHandler::class,
        ],
        Handler\SaveHandler::ROUTE_NAME
    );
    $app->post(
        Handler\UploadHandler::ROUTE,
        Handler\UploadHandler::class,
        Handler\UploadHandler::ROUTE_NAME
    );
};
