<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiApp\Authentication\Adapter;
use CooarchiApp\ConfigProvider;
use CooarchiEntities;
use CooarchiQueries;
use Doctrine\ORM\EntityManager;
use Exception;
use Laminas\Crypt\Password\Bcrypt;
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
use function mb_strlen;
use function random_int;

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
        CooarchiQueries\FindUser $findUserQuery,
        TemplateRendererInterface $template,
        UrlHelper $urlHelper
    ) {
        $this->entityManager = $entityManager;
        $this->findInvitationQuery = $findInvitationQuery;
        $this->findUserQuery = $findUserQuery;
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
            if (empty($postAttributes['name'])) {
                $data['errorInput'] = 'name';
                $data['error'] = 'Name cannot be empty';
                return new HtmlResponse($this->template->render('app::registration', $data));
            }

            $name = filter_var($postAttributes['name'], FILTER_SANITIZE_STRING);
            if ($this->findUserQuery->byName($name) !== null) {
                $data['errorInput'] = 'name';
                $data['error'] = 'Name already exists - choose another one';
                $data['name'] = $name;
                return new HtmlResponse($this->template->render('app::registration', $data));
            }

            if (empty($postAttributes['password'])) {
                $data['errorInput'] = 'password';
                $data['error'] = 'Password cannot be empty';
                $data['name'] = $name;
                return new HtmlResponse($this->template->render('app::registration', $data));
            }

            $password = filter_var($postAttributes['password'], FILTER_SANITIZE_STRING);
            if (mb_strlen($password, ConfigProvider::ENCODING) < 12) {
                $data['errorInput'] = 'password';
                $data['error'] = 'Your password needs to be minimum 12 chars long';
                $data['name'] = $name;
                return new HtmlResponse($this->template->render('app::registration', $data));
            }

            try {
                $bcrypt = new Bcrypt();
                $bcrypt->setCost(Adapter::PASSWORD_COST);
                $passwordHash = $bcrypt->create($password);
                $userRecord = new CooarchiEntities\User($name, $passwordHash);
                $this->entityManager->persist($userRecord);
                $this->entityManager->flush();

                $flashMessages->addMessage(
                    'success',
                    sprintf('User %s was created', $userRecord->getName())
                );

                return new RedirectResponse($this->urlHelper->generate(HomeHandler::ROUTE_NAME));
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

        $data['name'] = 'kollektivistA' . random_int(1, 23422342);

        return new HtmlResponse($this->template->render('app::registration', $data));
    }
}
