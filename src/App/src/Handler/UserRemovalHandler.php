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

class UserRemovalHandler implements RequestHandlerInterface
{
    public const ROUTE = '/user-removal/{userId}';
    public const ROUTE_NAME = 'user-removal';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CooarchiQueries\FindUser
     */
    private $findUserQuery;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(
        EntityManager $entityManager,
        CooarchiQueries\FindUser $findUserQuery,
        UrlHelper $urlHelper
    ) {
        $this->entityManager = $entityManager;
        $this->findUserQuery = $findUserQuery;
        $this->urlHelper = $urlHelper;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /** @var Messages $flashMessages */
        $flashMessages = $request->getAttribute('flash');
        $userId = $request->getAttribute('userId');

        try {
            if (Uuid::isValid($userId) === false) {
                $flashMessages->addMessage('error', 'Invalid userId provided');
                return new RedirectResponse($this->urlHelper->generate(UserManagementHandler::ROUTE_NAME));
            }

            $userRecord = $this->findUserQuery->byId($userId);

            if ($userRecord === null) {
                $flashMessages->addMessage('error', 'User record not found');
                return new RedirectResponse($this->urlHelper->generate(UserManagementHandler::ROUTE_NAME));
            }

            $userName = $userRecord->getName();
            $this->entityManager->remove($userRecord);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            $flashMessages->addMessage(
                'error',
                sprintf('An error occurred while removing user %s', $userId)
            );
            return new RedirectResponse($this->urlHelper->generate(UserManagementHandler::ROUTE_NAME));
        }

        $flashMessages->addMessage(
            'success',
            sprintf('User %s was removed successfully', $userName)
        );

        return new RedirectResponse($this->urlHelper->generate(UserManagementHandler::ROUTE_NAME));
    }
}
