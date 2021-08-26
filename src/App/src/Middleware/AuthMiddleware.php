<?php

declare(strict_types=1);

namespace CooarchiApp\Middleware;

use CooarchiApp\Authentication;
use CooarchiApp\Handler\AuthStatusHandler;
use CooarchiApp\Handler\HomeHandler;
use CooarchiApp\Handler\RegistrationHandler;
use CooarchiEntities\User;
use Laminas\Crypt\Password\Bcrypt;
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
     * @var bool
     */
    private $isPublicReadable;

    /**
     * @var bool
     */
    private $isPublicWriteable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(
        AuthenticationService $authenticationService,
        UrlHelper $urlHelper,
        bool $isPublicReadable,
        bool $isPublicWriteable
    ) {
        $this->authenticationService = $authenticationService;
        $this->isPublicReadable = $isPublicReadable;
        $this->isPublicWriteable = $isPublicWriteable;
        $this->urlHelper = $urlHelper;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate) : ResponseInterface
    {
        $route = $request->getAttribute(RouteResult::class);
        $routeName = $route->getMatchedRoute()->getName();

        $identity = $this->authenticationService->getIdentity();

        // If identity exist, we can skip further logic and just sent identity against next middleware
        if ($identity !== null) {
            return $delegate->handle($request->withAttribute('identity', $identity));
        }

        if ($routeName !== HomeHandler::ROUTE_NAME &&
            $routeName !== AuthStatusHandler::ROUTE_NAME &&
            $routeName !== RegistrationHandler::ROUTE_NAME &&
            $this->authenticationService->hasIdentity() === false
        ) {
            if ($this->isPublicReadable === true || $this->isPublicWriteable === true) {
                $travellaIdentity = new User('travella', 'foobar', User::ROLE_TRAVELLA);
                return $delegate->handle($request->withAttribute('identity', $travellaIdentity));
            }

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
