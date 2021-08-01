<?php

declare(strict_types=1);

namespace CooarchiApp\Middleware;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Mezzio\Helper\UrlHelper;

final class AuthFactory
{
    public function __invoke(ContainerInterface $container) : AuthMiddleware
    {
        return new AuthMiddleware(
            $container->get(AuthenticationService::class),
            $container->get(UrlHelper::class),
            $container->get('config')['isPublicReadable'] ?? false
        );
    }
}
