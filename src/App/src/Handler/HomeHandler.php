<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;

class HomeHandler implements RequestHandlerInterface
{
    public const ROUTE = '/';
    public const ROUTE_NAME = 'home';

    /**
     * @var array
     */
    private $cooArchiConfig;

    /**
     * @var TemplateRendererInterface
     */
    private $template;

    public function __construct(
        TemplateRendererInterface $template,
        array $cooArchiConfig
    ) {
        $this->cooArchiConfig = $cooArchiConfig;
        $this->template  = $template;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /** @var Messages $flashMessages */
        $flashMessages = $request->getAttribute('flash');

        $data = [];
        $data['welcomeText'] = $this->cooArchiConfig['welcomeText'];

        $messagesSuccess = $flashMessages->getMessage('success');
        if (is_array($messagesSuccess) === true) {
            $data['success'] = $messagesSuccess[0];
        }
        $messagesError = $flashMessages->getMessage('error');
        if (is_array($messagesError) === true) {
            $data['error'] = $messagesError[0];
        }

        return new HtmlResponse($this->template->render('app::home', $data));
    }
}
