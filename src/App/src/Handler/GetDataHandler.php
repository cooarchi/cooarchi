<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiEntities;
use CooarchiQueries;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class GetDataHandler implements RequestHandlerInterface
{
    public const ROUTE_NAME = 'data';

    /**
     * @var CooarchiQueries\GetElements
     */
    private $elementsQuery;

    /**
     * @var CooarchiQueries\GetElementRelations
     */
    private $elementRelationsQuery;

    /**
     * @var CooarchiQueries\GetRelationLabels
     */
    private $relationLabelsQuery;

    public function __construct(
        CooarchiQueries\GetElements $elementsQuery,
        CooarchiQueries\GetElementRelations $elementRelationsQuery,
        CooarchiQueries\GetRelationLabels $relationLabelsQuery
    ) {
        $this->elementsQuery = $elementsQuery;
        $this->elementRelationsQuery = $elementRelationsQuery;
        $this->relationLabelsQuery = $relationLabelsQuery;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $elements = $this->elementsQuery->all();
        $elementRelations = $this->elementRelationsQuery->all();

        return new JsonResponse(
            $this->buildJsonRepresentation($elementRelations)
        );
    }

    private function buildJsonRepresentation(array $elementRelations) : array
    {
        $json = [];

        /** @var CooarchiEntities\ElementRelation $relation */
        foreach ($elementRelations as $relation) {
            $json[] = [
                'name' => $relation->getRelationLabel()->getDescription(),
                'source' => [
                    'id' => $relation->getElementFrom()->getPubId(),
                    'name' => $relation->getElementFrom()->getInfo(),
                ],
                'target' => [
                    'id' => $relation->getElementTo()->getPubId(),
                    'name' => $relation->getElementTo()->getInfo(),
                ],
            ];
        }

        return $json;
    }
}
