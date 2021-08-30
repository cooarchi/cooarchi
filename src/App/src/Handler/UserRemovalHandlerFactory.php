<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiQueries;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

class UserRemovalHandlerFactory
{
    public function __invoke(ContainerInterface $container) : UserRemovalHandler
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');

        return new UserRemovalHandler(
            $entityManager,
            new CooarchiQueries\FindUser($entityManager),
            $container->get(UrlHelper::class)
        );
    }
}
