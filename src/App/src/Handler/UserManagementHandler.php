<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiQueries;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;
use function is_array;

class UserManagementHandler implements RequestHandlerInterface
{
    public const ROUTE = '/users';
    public const ROUTE_NAME = 'users';

    /**
     * @var CooarchiQueries\GetUsers
     */
    private $getUsersQuery;

    /**
     * @var TemplateRendererInterface
     */
    private $template;

    public function __construct(
        CooarchiQueries\GetUsers $getUsersQuery,
        TemplateRendererInterface $template
    ) {
        $this->getUsersQuery = $getUsersQuery;
        $this->template = $template;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /** @var Messages $flashMessages */
        $flashMessages = $request->getAttribute('flash');

        $data = [
            'users' => $this->getUsersQuery->all(),
        ];

        $messagesSuccess = $flashMessages->getMessage('success');
        if (is_array($messagesSuccess) === true) {
            $data['success'] = $messagesSuccess[0];
        }
        $messagesError = $flashMessages->getMessage('error');
        if (is_array($messagesError) === true) {
            $data['error'] = $messagesError[0];
        }

        return new HtmlResponse($this->template->render('app::users', $data));
    }
}
