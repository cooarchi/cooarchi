<?php
declare(strict_types=1);

namespace CooarchiApp\Command;

use CooarchiQueries\FindUser;
use Psr\Container\ContainerInterface;

final class CreateAdministrataCommandFactory
{
    public function __invoke(ContainerInterface $container) : CreateAdministrataCommand
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');

        return new CreateAdministrataCommand(
            $entityManager,
            new FindUser($entityManager)
        );
    }
}
