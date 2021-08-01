<?php

declare(strict_types=1);

namespace CooarchiApp\Authentication;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;

class AuthenticationServiceFactory
{
    public function __invoke(ContainerInterface $container) : AuthenticationService
    {
        return new AuthenticationService(
            null,
            $container->get(Adapter::class)
        );
    }
}