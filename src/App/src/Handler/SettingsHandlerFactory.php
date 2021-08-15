<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use LogicException;
use Psr\Container\ContainerInterface;

class SettingsHandlerFactory
{
    public function __invoke(ContainerInterface $container) : SettingsHandler
    {
        $cooarchiConfig = $container->get('config')['cooarchi'] ?? [];
        if ($cooarchiConfig === []) {
            throw new LogicException('cooarchi config is missing');
        }

        return new SettingsHandler(
            $cooarchiConfig
        );
    }
}
