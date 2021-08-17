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
use function filter_var;
use function is_array;

class RegistrationHandler implements RequestHandlerInterface
{
    public const ROUTE = '/register/{invitationHash}';
    public const ROUTE_NAME = 'register';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CooarchiQueries\FindInvitation
     */
    private $findInvitationQuery;

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
        TemplateRendererInterface $template,
        UrlHelper $urlHelper
    ) {
        $this->entityManager = $entityManager;
        $this->findInvitationQuery = $findInvitationQuery;
        $this->template = $template;
        $this->urlHelper = $urlHelper;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /** @var Messages $flashMessages */
        $flashMessages = $request->getAttribute('flash');

        $data = [];

        $invitationHash = $request->getAttribute('invitationHash', '');
        $invitationHash = filter_var($invitationHash, FILTER_SANITIZE_STRING);
        $data['invitationHash'] = $invitationHash;

        $invitationRecord = $this->findInvitationQuery->byHash($invitationHash);
        if ($invitationRecord === null) {
            $data['error'] = 'Invitation does not work - contact your admin';
            return new HtmlResponse($this->template->render('app::registration', $data));
        }

        if ($request->getMethod() === 'POST') {
            $postAttributes = $request->getParsedBody();
            if (empty($postAttributes['identifier'])) {
                $data['errorInput'] = 'identifier';
                $data['error'] = 'Identifier cannot be empty';
                return new HtmlResponse($this->template->render('app::registration', $data));
            }

            $identifier = $postAttributes['identifier'];
            if ($this->findInvitationQuery->byIdentifier($identifier) !== null) {
                $data['errorInput'] = 'identifier';
                $data['error'] = 'Identifier already exists - choose another one';
                $data['identifier'] = $identifier;
                return new HtmlResponse($this->template->render('app::registration', $data));
            }

            try {
                $invitationRecord = new CooarchiEntities\Invitation($identifier);
                $this->entityManager->persist($invitationRecord);
                $this->entityManager->flush();

                $flashMessages->addMessage(
                    'success',
                    sprintf('User %s was created', $invitationRecord->getIdentifier())
                );

                return new RedirectResponse($this->urlHelper->generate(self::ROUTE_NAME));
            } catch (Exception $exception) {
                $data['error'] = $exception->getMessage();
                return new HtmlResponse($this->template->render('app::registration', $data));
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

        return new HtmlResponse($this->template->render('app::registration', $data));
    }
}
