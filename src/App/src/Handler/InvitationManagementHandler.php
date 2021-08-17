<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiEntities;
use CooarchiQueries;
use Doctrine\ORM\EntityManager;
use Exception;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;
use function is_array;

class InvitationManagementHandler implements RequestHandlerInterface
{
    public const ROUTE = '/invitations';
    public const ROUTE_NAME = 'invitations';

    /**
     * @var string
     */
    private $backendUrl;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CooarchiQueries\FindInvitation
     */
    private $findInvitationQuery;

    /**
     * @var CooarchiQueries\GetInvitations
     */
    private $getInvitationsQuery;

    /**
     * @var TemplateRendererInterface
     */
    private $template;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(
        EntityManager $entityManager,
        CooarchiQueries\FindInvitation $findInvitationQuery,
        CooarchiQueries\GetInvitations $getInvitationsQuery,
        TemplateRendererInterface $template,
        UrlHelper $urlHelper,
        string $backendUrl
    ) {
        $this->backendUrl = $backendUrl;
        $this->entityManager = $entityManager;
        $this->findInvitationQuery = $findInvitationQuery;
        $this->getInvitationsQuery = $getInvitationsQuery;
        $this->template = $template;
        $this->urlHelper = $urlHelper;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /** @var Messages $flashMessages */
        $flashMessages = $request->getAttribute('flash');

        $data = [];

        $data['invitations'] = $this->getInvitationsQuery->all();
        $data['backendUrl'] = $this->backendUrl;

        if ($request->getMethod() === 'POST') {
            $postAttributes = $request->getParsedBody();
            if (empty($postAttributes['identifier'])) {
                $data['errorInput'] = 'identifier';
                $data['error'] = 'Identifier cannot be empty';
                return new HtmlResponse($this->template->render('app::invitations', $data));
            }

            $identifier = $postAttributes['identifier'];
            if ($this->findInvitationQuery->byIdentifier($identifier) !== null) {
                $data['errorInput'] = 'identifier';
                $data['error'] = 'Identifier already exists - choose another one';
                $data['identifier'] = $identifier;
                return new HtmlResponse($this->template->render('app::invitations', $data));
            }

            try {
                $invitationRecord = new CooarchiEntities\Invitation($identifier);
                $this->entityManager->persist($invitationRecord);
                $this->entityManager->flush();

                $flashMessages->addMessage(
                    'success',
                    sprintf('Invitation %s was created', $invitationRecord->getIdentifier())
                );

                return new RedirectResponse($this->urlHelper->generate(self::ROUTE_NAME));
            } catch (Exception $exception) {
                $data['error'] = $exception->getMessage();
                return new HtmlResponse($this->template->render('app::invitations', $data));
            }
        }

        $messagesSuccess = $flashMessages->getMessage('success');
        if (is_array($messagesSuccess) === true) {
            $data['success'] = $messagesSuccess[0];
        }
        $messagesError = $flashMessages->getMessage('error');
        if (is_array($messagesError) === true) {
            $data['error'] = $messagesError[0];
        }

        return new HtmlResponse($this->template->render('app::invitations', $data));
    }
}
