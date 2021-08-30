<?php
declare(strict_types=1);

namespace CooarchiApp\Middleware;

use Interop\Container\ContainerInterface;
use LogicException;
use Mezzio\Template\TemplateRendererInterface;

final class TemplateVariablesFactory
{
    public function __invoke(ContainerInterface $container) : TemplateVariablesMiddleware
    {
        $cooarchiConfig = $container->get('config')['cooarchi'] ?? [];
        if ($cooarchiConfig === []) {
            throw new LogicException('cooarchi config is missing');
        }

        return new TemplateVariablesMiddleware(
            $container->get(TemplateRendererInterface::class),
            $cooarchiConfig
        );
    }
}
