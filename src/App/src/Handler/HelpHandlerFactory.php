<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

final class HelpHandlerFactory
{
    public function __invoke(ContainerInterface $container) : HelpHandler
    {
        return new HelpHandler(
            $container->get(TemplateRendererInterface::class)
        );
    }
}
