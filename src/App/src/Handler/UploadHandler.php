<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiEntities;
use Doctrine\ORM\EntityManager;
use Exception;
use InvalidArgumentException;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\UploadedFile;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\UuidFactory;
use function file_put_contents;
use function sprintf;

class UploadHandler implements RequestHandlerInterface
{
    public const ROUTE = '/upload';
    public const ROUTE_NAME = 'upload';

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var UuidFactory
     */
    private $uuidFactory;

    public function __construct(
        EntityManager $entityManager,
        UuidFactory $uuidFactory,
        string $basePath
    ) {
        $this->basePath = $basePath;
        $this->entityManager = $entityManager;
        $this->uuidFactory = $uuidFactory;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(['error' => 'Invalid request method'], 500);
        }

        try {
            $files = $request->getUploadedFiles();
            if (isset($files['file']) === false) {
                throw new InvalidArgumentException('No file provided for upload');
            }

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $files['file'];

            $mimeType = $uploadedFile->getClientMediaType();
            $label = null; // @todo not implemented in frontend right now

            $file = new CooarchiEntities\File(
                $this->uuidFactory->uuid1(),
                $mimeType,
                (int) $uploadedFile->getSize(),
                $label
            );

            $this->entityManager->persist($file);
            $this->entityManager->flush();

            $extension = $this->getFileExtension($file->getMimeType());
            $fileName = sprintf(
                '%s.%s',
                $file->getPubId(),
                $extension
            );
            $filePath = sprintf(
                '%s/public/files/%s',
                $this->basePath,
                $fileName
            );

            $uploadedFile->moveTo($filePath);
        } catch (Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 500);
        }

        return new JsonResponse(
            ['filename' => sprintf('/files/%s', $fileName)],
            200
        );
    }

    private function getFileExtension(string $mimeType) : string
    {
        switch ($mimeType) {
            case 'audio/mp3' :
                return 'mp3';
            case 'audio/wav' :
                return 'wav';
            case 'image/jpeg' :
            case 'image/jpg' :
                return 'jpg';
            case 'image/png' :
                return 'png';
            case 'image/gif' :
                return 'gif';
            default :
                throw new InvalidArgumentException(sprintf('MimeType "%s" is not supported', $mimeType));
        }
    }
}
