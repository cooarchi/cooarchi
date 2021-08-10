<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Helper\UrlHelper;

final class LogoutHandler implements RequestHandlerInterface
{
    public const ROUTE = '/logout';
    public const ROUTE_NAME = 'logout';

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    public function __construct(AuthenticationService $authenticationService, UrlHelper $urlHelper)
    {
        $this->authenticationService = $authenticationService;
        $this->urlHelper = $urlHelper;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $this->authenticationService->clearIdentity();

        return new RedirectResponse($this->urlHelper->generate(HomeHandler::ROUTE_NAME));
    }
}
