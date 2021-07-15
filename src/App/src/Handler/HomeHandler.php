<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HomeHandler implements RequestHandlerInterface
{
    public const ROUTE = '/home';
    public const ROUTE_NAME = 'home';

    /**
     * @var TemplateRendererInterface
     */
    private $template;

    public function __construct(
        TemplateRendererInterface $template
    ) {
        $this->template  = $template;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $data = [];

        // $this->templateRenderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'identity', $user);

        return new HtmlResponse($this->template->render('app::home', $data));
    }
}
