<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiApp\Helper;
use CooarchiEntities;
use CooarchiQueries;
use DateInterval;
use DateTime;
use DateTimeZone;
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
        $delta = (int) ($request->getQueryParams()['delta'] ?? 0);
        $deltaSinceDateTime = new DateTime('now', new DateTimeZone('UTC'));
        $deltaSinceDateTime->sub(new DateInterval('PT1M'));

        try {
            $elements = $this->getElements($delta, $deltaSinceDateTime);
            $elementRelations = $this->getRelations($delta, $deltaSinceDateTime);
            $data = Helper\JsonRepresentation::create($elements, $elementRelations);
        } catch (Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 500);
        }

        return new JsonResponse($data);
    }

    private function getElements(int $delta, DateTime $deltaSinceDateTime) : array
    {
        if ($delta === 1) {
            return $this->elementsQuery->delta($deltaSinceDateTime);
        }

        return $this->elementsQuery->all();
    }

    private function getRelations(int $delta, DateTime $deltaSinceDateTime) : array
    {
        if ($delta === 1) {
            return $this->elementRelationsQuery->delta($deltaSinceDateTime);
        }

        return $this->elementRelationsQuery->all();
    }
}
