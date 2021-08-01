<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiQueries;
use Psr\Container\ContainerInterface;

final class GetDataHandlerFactory
{
    public function __invoke(ContainerInterface $container) : GetDataHandler
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');

        return new GetDataHandler(
            new CooarchiQueries\GetElements($entityManager),
            new CooarchiQueries\GetElementRelations($entityManager)
        );
    }
}
