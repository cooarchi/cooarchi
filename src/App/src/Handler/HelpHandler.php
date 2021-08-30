<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;

class HelpHandler implements RequestHandlerInterface
{
    public const ROUTE = '/help';
    public const ROUTE_NAME = 'help';

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
        return new HtmlResponse($this->template->render('app::help'));
    }
}
