<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiQueries;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

class ContentRemovalHandlerFactory
{
    public function __invoke(ContainerInterface $container) : ContentRemovalHandler
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');

        return new ContentRemovalHandler(
            $entityManager,
            new CooarchiQueries\FindElement($entityManager),
            new CooarchiQueries\RemoveElementRelations($entityManager),
            $container->get(UrlHelper::class)
        );
    }
}
