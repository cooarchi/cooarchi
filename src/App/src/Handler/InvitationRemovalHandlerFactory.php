<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiQueries;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

class InvitationRemovalHandlerFactory
{
    public function __invoke(ContainerInterface $container) : InvitationRemovalHandler
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');

        return new InvitationRemovalHandler(
            $entityManager,
            new CooarchiQueries\FindInvitation($entityManager),
            $container->get(UrlHelper::class)
        );
    }
}
