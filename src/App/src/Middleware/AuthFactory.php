<?php

declare(strict_types=1);

namespace CooarchiApp\Middleware;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use LogicException;
use Mezzio\Helper\UrlHelper;

final class AuthFactory
{
    public function __invoke(ContainerInterface $container) : AuthMiddleware
    {
        $cooarchiConfig = $container->get('config')['cooarchi'] ?? [];
        if ($cooarchiConfig === []) {
            throw new LogicException('cooarchi config is missing');
        }

        return new AuthMiddleware(
            $container->get(AuthenticationService::class),
            $container->get(UrlHelper::class),
            $cooarchiConfig['isPublicReadable'] ?? false,
            $cooarchiConfig['isPublicWriteable'] ?? false
        );
    }
}
