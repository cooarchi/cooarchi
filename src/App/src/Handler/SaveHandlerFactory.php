<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiQueries;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

class SaveHandlerFactory
{
    public function __invoke(ContainerInterface $container) : SaveHandler
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');

        return new SaveHandler(
            $entityManager,
            new CooarchiQueries\FindElement($entityManager),
            new CooarchiQueries\FindElementRelation($entityManager),
            new CooarchiQueries\FindRelationLabel($entityManager)
        );
    }
}
