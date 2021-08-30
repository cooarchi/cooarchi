<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiQueries;
use LogicException;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

class FileRemovalHandlerFactory
{
    public function __invoke(ContainerInterface $container) : FileRemovalHandler
    {
        $basePath = $container->get('config')['basePath'] ?? null;
        if ($basePath === null) {
            throw new LogicException('basePath config value is missing');
        }

        $entityManager = $container->get('doctrine.entity_manager.orm_default');

        return new FileRemovalHandler(
            $entityManager,
            new CooarchiQueries\FindFile($entityManager),
            $container->get(UrlHelper::class),
            $basePath
        );
    }
}
