<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiApp\Helper;
use CooarchiEntities;
use CooarchiQueries;
use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class GetDataHandler implements RequestHandlerInterface
{
    public const ROUTE = '/data';
    public const ROUTE_NAME = 'data';

    /**
     * @var CooarchiQueries\GetElements
     */
    private $elementsQuery;

    /**
     * @var CooarchiQueries\GetElementRelations
     */
    private $elementRelationsQuery;

    public function __construct(
        CooarchiQueries\GetElements $elementsQuery,
        CooarchiQueries\GetElementRelations $elementRelationsQuery
    ) {
        $this->elementsQuery = $elementsQuery;
        $this->elementRelationsQuery = $elementRelationsQuery;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $delta = $request->getQueryParams()['delta'] ?? null;

        try {
            $elements = $this->elementsQuery->all();
            $elementRelations = $this->elementRelationsQuery->all();
            $data = Helper\JsonRepresentation::create($elements, $elementRelations);
        } catch (Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 500);
        }

        return new JsonResponse($data);
    }
}
