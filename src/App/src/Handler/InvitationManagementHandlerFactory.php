<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiQueries;
use LogicException;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class InvitationManagementHandlerFactory
{
    public function __invoke(ContainerInterface $container) : InvitationManagementHandler
    {
        $cooarchiConfig = $container->get('config')['cooarchi'] ?? [];
        if ($cooarchiConfig === []) {
            throw new LogicException('cooarchi config is missing');
        }
        if (isset($cooarchiConfig['backendUrl']) === false) {
            throw new LogicException('cooarchi backendUrl config is missing');
        }

        $entityManager = $container->get('doctrine.entity_manager.orm_default');

        return new InvitationManagementHandler(
            $entityManager,
            new CooarchiQueries\FindInvitation($entityManager),
            new CooarchiQueries\GetInvitations($entityManager),
            $container->get(TemplateRendererInterface::class),
            $container->get(UrlHelper::class),
            $cooarchiConfig['backendUrl']
        );
    }
}
