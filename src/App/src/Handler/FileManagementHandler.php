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

class FileManagementHandler implements RequestHandlerInterface
{
    public const ROUTE = '/file-management';
    public const ROUTE_NAME = 'file-management';

    /**
     * @var CooarchiQueries\GetFiles
     */
    private $getFilesQuery;

    /**
     * @var TemplateRendererInterface
     */
    private $template;

    public function __construct(
        CooarchiQueries\GetFiles $getFilesQuery,
        TemplateRendererInterface $template
    ) {
        $this->getFilesQuery = $getFilesQuery;
        $this->template = $template;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /** @var Messages $flashMessages */
        $flashMessages = $request->getAttribute('flash');

        $data = [
            'files' => $this->getFilesQuery->all(),
        ];

        $messagesSuccess = $flashMessages->getMessage('success');
        if (is_array($messagesSuccess) === true) {
            $data['success'] = $messagesSuccess[0];
        }
        $messagesError = $flashMessages->getMessage('error');
        if (is_array($messagesError) === true) {
            $data['error'] = $messagesError[0];
        }

        return new HtmlResponse($this->template->render('app::file-management', $data));
    }
}
