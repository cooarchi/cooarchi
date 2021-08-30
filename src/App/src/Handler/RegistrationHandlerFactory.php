<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiQueries;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class RegistrationHandlerFactory
{
    public function __invoke(ContainerInterface $container) : RegistrationHandler
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');

        return new RegistrationHandler(
            $entityManager,
            new CooarchiQueries\FindInvitation($entityManager),
            new CooarchiQueries\FindUser($entityManager),
            $container->get(TemplateRendererInterface::class),
            $container->get(UrlHelper::class)
        );
    }
}
