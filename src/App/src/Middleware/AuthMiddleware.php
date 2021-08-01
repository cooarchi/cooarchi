<?php

declare(strict_types=1);

namespace CooarchiApp\Middleware;

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
     * @var
     */
    private $isPublicReadable;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(
        AuthenticationService $authenticationService,
        UrlHelper $urlHelper,
        bool $isPublicReadable
    ) {
        $this->authenticationService = $authenticationService;
        $this->isPublicReadable = $isPublicReadable;
        $this->urlHelper = $urlHelper;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate) : ResponseInterface
    {
        if ($this->authenticationService->hasIdentity() === false) {
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
