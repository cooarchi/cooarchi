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

final class PermissionMiddleware implements MiddlewareInterface
{
    /**
     * @var Rbac
     */
    private $rbac;

    /**
     * @var TemplateRendererInterface
     */
    private $templateRenderer;

    public function __construct(
        Rbac $rbac,
        TemplateRendererInterface $templateRenderer
    ) {
        $this->rbac = $rbac;
        $this->templateRenderer = $templateRenderer;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate) : ResponseInterface
    {
        /** @var CooarchiEntities\User|false $user */
        $user = $request->getAttribute('identity', false);

        if ($user === false) {
            return new EmptyResponse(StatusCode\All::UNAUTHORIZED);
        }

        $route = $request->getAttribute(RouteResult::class);
        $routeName = $route->getMatchedRoute()->getName();
        if ($this->rbac->isGranted($user->getRole(), $routeName) === false) {
            return new EmptyResponse(StatusCode\All::FORBIDDEN);
        }

        $this->templateRenderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'identity',
            $user
        );

        return $delegate->handle($request);
    }
}
