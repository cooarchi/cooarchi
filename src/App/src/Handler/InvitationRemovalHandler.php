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

class InvitationRemovalHandler implements RequestHandlerInterface
{
    public const ROUTE = '/invitation-removal/{invitationId}';
    public const ROUTE_NAME = 'invitation-removal';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CooarchiQueries\FindInvitation
     */
    private $findInvitationQuery;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(
        EntityManager $entityManager,
        CooarchiQueries\FindInvitation $findInvitationQuery,
        UrlHelper $urlHelper
    ) {
        $this->entityManager = $entityManager;
        $this->findInvitationQuery = $findInvitationQuery;
        $this->urlHelper = $urlHelper;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /** @var Messages $flashMessages */
        $flashMessages = $request->getAttribute('flash');
        $invitationId = $request->getAttribute('invitationId');

        try {
            if (Uuid::isValid($invitationId) === false) {
                $flashMessages->addMessage('error', 'Invalid invitationId provided');
                return new RedirectResponse($this->urlHelper->generate(InvitationManagementHandler::ROUTE_NAME));
            }

            $invitationRecord = $this->findInvitationQuery->byId($invitationId);

            if ($invitationRecord === null) {
                $flashMessages->addMessage('error', 'Invitation record not found');
                return new RedirectResponse($this->urlHelper->generate(InvitationManagementHandler::ROUTE_NAME));
            }

            $invitationIdentifier = $invitationRecord->getIdentifier();
            $this->entityManager->remove($invitationRecord);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            $flashMessages->addMessage(
                'error',
                sprintf('An error occurred while removing invitation %s', $invitationId)
            );
            return new RedirectResponse($this->urlHelper->generate(InvitationManagementHandler::ROUTE_NAME));
        }

        $flashMessages->addMessage(
            'success',
            sprintf('Invitation %s was removed successfully', $invitationIdentifier)
        );

        return new RedirectResponse($this->urlHelper->generate(InvitationManagementHandler::ROUTE_NAME));
    }
}
