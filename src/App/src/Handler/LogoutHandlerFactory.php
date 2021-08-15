<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Mezzio\Helper\UrlHelper;

final class LogoutHandlerFactory
{
    public function __invoke(ContainerInterface $container) : LogoutHandler
    {
        return new LogoutHandler(
            $container->get(AuthenticationService::class),
            $container->get(UrlHelper::class)
        );
    }
}
