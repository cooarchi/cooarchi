<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiApp\Authentication;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;

final class LoginHandlerFactory
{
    public function __invoke(ContainerInterface $container) : LoginHandler
    {
        return new LoginHandler(
            $container->get(Authentication\Adapter::class),
            $container->get(AuthenticationService::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(UrlHelper::class)
        );
    }
}
