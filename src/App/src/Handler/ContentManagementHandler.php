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

class ContentManagementHandler implements RequestHandlerInterface
{
    public const ROUTE = '/content-management';
    public const ROUTE_NAME = 'content-management';

    /**
     * @var CooarchiQueries\GetElementRelations
     */
    private $getElementRelationsQuery;

    /**
     * @var CooarchiQueries\GetElements
     */
    private $getElementsQuery;

    /**
     * @var TemplateRendererInterface
     */
    private $template;

    public function __construct(
        CooarchiQueries\GetElementRelations $getElementRelationsQuery,
        CooarchiQueries\GetElements $getElementsQuery,
        TemplateRendererInterface $template
    ) {
        $this->getElementRelationsQuery = $getElementRelationsQuery;
        $this->getElementsQuery = $getElementsQuery;
        $this->template = $template;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /** @var Messages $flashMessages */
        $flashMessages = $request->getAttribute('flash');

        $data = [
            'elements' => $this->getElementsQuery->all(),
        ];

        $messagesSuccess = $flashMessages->getMessage('success');
        if (is_array($messagesSuccess) === true) {
            $data['success'] = $messagesSuccess[0];
        }
        $messagesError = $flashMessages->getMessage('error');
        if (is_array($messagesError) === true) {
            $data['error'] = $messagesError[0];
        }

        return new HtmlResponse($this->template->render('app::content-management', $data));
    }
}
