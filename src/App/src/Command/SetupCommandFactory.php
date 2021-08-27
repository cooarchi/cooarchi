<?php
declare(strict_types=1);

namespace CooarchiApp\Command;

use LogicException;
use Psr\Container\ContainerInterface;

final class SetupCommandFactory
{
    public function __invoke(ContainerInterface $container) : SetupCommand
    {
        $basePath = $container->get('config')['basePath'] ?? '';
        if ($basePath === '') {
            throw new LogicException('config basePath is missing');
        }

        return new SetupCommand(
            $basePath
        );
    }
}
