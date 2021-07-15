<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiEntities;
use CooarchiQueries;
use Doctrine\ORM\EntityManager;
use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Json\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use function filter_var;
use function trim;

class UploadHandler implements RequestHandlerInterface
{
    public const ROUTE = '/upload';
    public const ROUTE_NAME = 'upload';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CooarchiQueries\FindElement
     */
    private $findElementQuery;

    public function __construct(
        EntityManager $entityManager,
        CooarchiQueries\FindElement $findElementQuery
    ) {
        $this->entityManager = $entityManager;
        $this->findElementQuery = $findElementQuery;
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
            $bodyAttributes = $request->getBody()->getContents();
            $bodySize = $request->getBody()->getSize();

            \Doctrine\Common\Util\Debug::dump($bodyAttributes);
            exit();
            $elementFrom = $this->findElementQuery->byInfo($elementFromText);
        } catch (Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 500);
        }

        return new JsonResponse(
            [],
            200
        );
    }
}
