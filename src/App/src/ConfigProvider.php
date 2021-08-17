<?php

declare(strict_types=1);

namespace CooarchiApp;

use Laminas\Authentication\AuthenticationService;
use function dirname;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    public const ENCODING = 'UTF-8';

    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'basePath' => dirname(__DIR__, 3),
            'templates' => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'invokables' => [
                Handler\AuthStatusHandler::class => Handler\AuthStatusHandler::class,
                Handler\PingHandler::class => Handler\PingHandler::class,
                Middleware\SlimFlashMiddleware::class => Middleware\SlimFlashMiddleware::class,
            ],
            'factories'  => [
                AuthenticationService::class => Authentication\AuthenticationServiceFactory::class,
                Authentication\Adapter::class => Authentication\AdapterFactory::class,
                Handler\GetDataHandler::class => Handler\GetDataHandlerFactory::class,
                Handler\HomeHandler::class => Handler\HomeHandlerFactory::class,
                Handler\InvitationManagementHandler::class => Handler\InvitationManagementHandlerFactory::class,
                Handler\InvitationRemovalHandler::class => Handler\InvitationRemovalHandlerFactory::class,
                Handler\LoginHandler::class => Handler\LoginHandlerFactory::class,
                Handler\LogoutHandler::class => Handler\LogoutHandlerFactory::class,
                Handler\SaveHandler::class => Handler\SaveHandlerFactory::class,
                Handler\SettingsHandler::class => Handler\SettingsHandlerFactory::class,
                Handler\UploadHandler::class => Handler\UploadHandlerFactory::class,
                Middleware\AuthMiddleware::class => Middleware\AuthFactory::class,
                Middleware\PermissionMiddleware::class => Middleware\PermissionFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates() : array
    {
        $templatePath = dirname(__DIR__);

        return [
            'paths' => [
                'app'    => [$templatePath . '/templates/app'],
                'auth'   => [$templatePath . '/templates/auth'],
                'error'  => [$templatePath . '/templates/error'],
                'layout' => [$templatePath . '/templates/layout'],
            ],
        ];
    }
}
