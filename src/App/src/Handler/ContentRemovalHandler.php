<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiQueries;
use Doctrine\ORM\EntityManager;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Helper\UrlHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;
use Slim\Flash\Messages;

class ContentRemovalHandler implements RequestHandlerInterface
{
    public const ROUTE = '/content-removal/{elementId}';
    public const ROUTE_NAME = 'content-removal';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CooarchiQueries\FindElement
     */
    private $findElementQuery;

    /**
     * @var CooarchiQueries\RemoveElementRelations
     */
    private $removeElementRelationsQuery;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(
        EntityManager $entityManager,
        CooarchiQueries\FindElement $findElementQuery,
        CooarchiQueries\RemoveElementRelations $removeElementRelationsQuery,
        UrlHelper $urlHelper
    ) {
        $this->entityManager = $entityManager;
        $this->findElementQuery = $findElementQuery;
        $this->removeElementRelationsQuery = $removeElementRelationsQuery;
        $this->urlHelper = $urlHelper;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /** @var Messages $flashMessages */
        $flashMessages = $request->getAttribute('flash');
        $elementId = $request->getAttribute('elementId');

        try {
            if (Uuid::isValid($elementId) === false) {
                $flashMessages->addMessage('error', 'Invalid elementId provided');
                return new RedirectResponse($this->urlHelper->generate(ContentManagementHandler::ROUTE_NAME));
            }

            $elementRecord = $this->findElementQuery->byPubId($elementId);

            if ($elementRecord === null) {
                $flashMessages->addMessage('error', 'Element record not found');
                return new RedirectResponse($this->urlHelper->generate(ContentManagementHandler::ROUTE_NAME));
            }

            $this->removeElementRelationsQuery->byElement($elementRecord);

            $elementLabel = $elementRecord->getLabel();
            $this->entityManager->remove($elementRecord);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            $flashMessages->addMessage(
                'error',
                sprintf('An error occurred while removing element %s', $elementId)
            );
            return new RedirectResponse($this->urlHelper->generate(ContentManagementHandler::ROUTE_NAME));
        }

        $flashMessages->addMessage(
            'success',
            sprintf('Element %s was removed successfully', $elementLabel)
        );

        return new RedirectResponse($this->urlHelper->generate(ContentManagementHandler::ROUTE_NAME));
    }
}
