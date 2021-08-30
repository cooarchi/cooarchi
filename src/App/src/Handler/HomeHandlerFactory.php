<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use LogicException;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

final class HomeHandlerFactory
{
    public function __invoke(ContainerInterface $container) : HomeHandler
    {
        $cooarchiConfig = $container->get('config')['cooarchi'] ?? [];
        if ($cooarchiConfig === []) {
            throw new LogicException('cooarchi config is missing');
        }

        return new HomeHandler(
            $container->get(TemplateRendererInterface::class),
            $cooarchiConfig
        );
    }
}
