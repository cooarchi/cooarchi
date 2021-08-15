<?php

declare(strict_types=1);

namespace CooarchiApp\Middleware;

use CooarchiApp\Handler\AuthStatusHandler;
use CooarchiApp\Handler\HomeHandler;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Helper\UrlHelper;

final class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(
        AuthenticationService $authenticationService,
        UrlHelper $urlHelper
    ) {
        $this->authenticationService = $authenticationService;
        $this->urlHelper = $urlHelper;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate) : ResponseInterface
    {
        $route = $request->getAttribute(RouteResult::class);
        $routeName = $route->getMatchedRoute()->getName();

        if ($routeName !== HomeHandler::ROUTE_NAME &&
            $routeName !== AuthStatusHandler::ROUTE_NAME &&
            $this->authenticationService->hasIdentity() === false
        ) {
            return new RedirectResponse($this->urlHelper->generate('login'));
        }

        return $delegate->handle(
            $request->withAttribute(
                'identity',
                $this->authenticationService->getIdentity()
            )
        );
    }

}
