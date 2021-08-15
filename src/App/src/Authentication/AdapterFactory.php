<?php

declare(strict_types=1);

namespace CooarchiApp\Authentication;

use CooarchiQueries;
use Interop\Container\ContainerInterface;

final class AdapterFactory
{
    public function __invoke(ContainerInterface $container) : Adapter
    {
        return new Adapter(
            new CooarchiQueries\FindUser($container->get('doctrine.entity_manager.orm_default'))
        );
    }
}
