<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiQueries;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class FileManagementHandlerFactory
{
    public function __invoke(ContainerInterface $container) : FileManagementHandler
    {
        return new FileManagementHandler(
            new CooarchiQueries\GetFiles($container->get('doctrine.entity_manager.orm_default')),
            $container->get(TemplateRendererInterface::class)
        );
    }
}
