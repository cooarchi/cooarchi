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
use function is_file;
use function sprintf;
use function unlink;

class FileRemovalHandler implements RequestHandlerInterface
{
    public const ROUTE = '/file-removal/{fileId}';
    public const ROUTE_NAME = 'file-removal';

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CooarchiQueries\FindFile
     */
    private $findFileQuery;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(
        EntityManager $entityManager,
        CooarchiQueries\FindFile $findFileQuery,
        UrlHelper $urlHelper,
        string $basePath
    ) {
        $this->basePath = $basePath;
        $this->entityManager = $entityManager;
        $this->findFileQuery = $findFileQuery;
        $this->urlHelper = $urlHelper;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /** @var Messages $flashMessages */
        $flashMessages = $request->getAttribute('flash');
        $fileId = $request->getAttribute('fileId');

        try {
            if (Uuid::isValid($fileId) === false) {
                $flashMessages->addMessage('error', 'Invalid fileId provided');
                return new RedirectResponse($this->urlHelper->generate(FileManagementHandler::ROUTE_NAME));
            }

            $fileRecord = $this->findFileQuery->byPubId($fileId);

            if ($fileRecord === null) {
                $flashMessages->addMessage('error', 'File record not found');
                return new RedirectResponse($this->urlHelper->generate(FileManagementHandler::ROUTE_NAME));
            }

            $filePath = sprintf(
                '%s/public/files/%s.%s',
                $this->basePath,
                $fileRecord->getPubId(),
                $fileRecord->getExtension()
            );
            if (is_file($filePath) === true) {
                unlink($filePath);
            }

            $filePubId = $fileRecord->getPubId();
            $this->entityManager->remove($fileRecord);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            $flashMessages->addMessage(
                'error',
                sprintf('An error occurred while removing file %s: %s', $fileId, $exception->getMessage())
            );
            return new RedirectResponse($this->urlHelper->generate(FileManagementHandler::ROUTE_NAME));
        }

        $flashMessages->addMessage(
            'success',
            sprintf('File %s was removed successfully', $filePubId)
        );

        return new RedirectResponse($this->urlHelper->generate(FileManagementHandler::ROUTE_NAME));
    }
}
