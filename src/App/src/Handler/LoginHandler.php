<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiApp\Authentication;
use Laminas\Authentication\AuthenticationService;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LoginHandler implements RequestHandlerInterface
{
    public const ROUTE = '/login';
    public const ROUTE_NAME = 'login';
    public const TEMPLATE = 'auth::login';

    /**
     * @var Authentication\Adapter
     */
    private $authenticationAdapter;

    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * @var Template\TemplateRendererInterface
     */
    private $template;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(
        Authentication\Adapter $authenticationAdapter,
        AuthenticationService $authenticationService,
        Template\TemplateRendererInterface $template,
        UrlHelper $urlHelper
    ) {
        $this->authenticationAdapter = $authenticationAdapter;
        $this->authenticationService = $authenticationService;
        $this->template = $template;
        $this->urlHelper = $urlHelper;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            return $this->authenticate($request);
        }

        return new HtmlResponse($this->template->render(self::TEMPLATE, []));
    }

    private function authenticate(ServerRequestInterface $request) : ResponseInterface
    {
        $params = $request->getParsedBody();

        if (empty($params['name'])) {
            return new HtmlResponse(
                $this->template->render(
                    self::TEMPLATE,
                    [
                        'error' => 'The name field cannot be empty',
                        'errorInput' => 'name',
                    ]
                )
            );
        }

        if (empty($params['password'])) {
            return new HtmlResponse(
                $this->template->render(
                    self::TEMPLATE,
                    [
                        'name' => $params['name'],
                        'error' => 'The password field cannot be empty',
                        'errorInput' => 'password',
                    ]
                )
            );
        }

        $this->authenticationAdapter->setName($params['name']);
        $this->authenticationAdapter->setPassword($params['password']);

        $result = $this->authenticationService->authenticate();

        if ($result->isValid() === false) {
            return new HtmlResponse(
                $this->template->render(
                    self::TEMPLATE,
                    [
                        'name' => $params['name'],
                        'error' => 'The credentials provided are not valid',
                        'errorInput' => 'name',
                    ]
                )
            );
        }

        return new RedirectResponse($this->urlHelper->generate(HomeHandler::ROUTE));
    }
}
