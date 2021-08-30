<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiQueries;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class ContentManagementHandlerFactory
{
    public function __invoke(ContainerInterface $container) : ContentManagementHandler
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');

        return new ContentManagementHandler(
            new CooarchiQueries\GetElementRelations($entityManager),
            new CooarchiQueries\GetElements($entityManager),
            $container->get(TemplateRendererInterface::class)
        );
    }
}
