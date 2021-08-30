<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiQueries;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class UserManagementHandlerFactory
{
    public function __invoke(ContainerInterface $container) : UserManagementHandler
    {
        return new UserManagementHandler(
            new CooarchiQueries\GetUsers($container->get('doctrine.entity_manager.orm_default')),
            $container->get(TemplateRendererInterface::class)
        );
    }
}
