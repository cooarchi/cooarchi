<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiQueries;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

class UploadHandlerFactory
{
    public function __invoke(ContainerInterface $container) : UploadHandler
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');

        return new UploadHandler(
            $entityManager,
            new CooarchiQueries\FindElement($entityManager)
        );
    }
}
