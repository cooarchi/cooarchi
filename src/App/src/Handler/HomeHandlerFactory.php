<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

final class HomeHandlerFactory
{
    public function __invoke(ContainerInterface $container) : HomeHandler
    {
        return new HomeHandler(
            $container->get(TemplateRendererInterface::class)
        );
    }
}
