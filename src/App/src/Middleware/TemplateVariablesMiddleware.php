<?php
declare(strict_types=1);

namespace CooarchiApp\Middleware;

use CooarchiEntities;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Permissions\Rbac\Rbac;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Teapot\StatusCode;

final class TemplateVariablesMiddleware implements MiddlewareInterface
{
    /**
     * @var array
     */
    private $cooArchiConfig;

    /**
     * @var TemplateRendererInterface
     */
    private $templateRenderer;

    public function __construct(
        TemplateRendererInterface $templateRenderer,
        array $cooArchiConfig
    ) {
        $this->cooArchiConfig = $cooArchiConfig;
        $this->templateRenderer = $templateRenderer;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate) : ResponseInterface
    {
        $user = $request->getAttribute('identity', false);

        $this->templateRenderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'identity',
            $user
        );

        $this->templateRenderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'cooArchiName',
            $this->cooArchiConfig['name']
        );

        return $delegate->handle($request);
    }
}
