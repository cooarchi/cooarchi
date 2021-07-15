<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiEntities;
use CooarchiQueries;
use Exception;
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

    public function __construct(
        CooarchiQueries\GetElements $elementsQuery,
        CooarchiQueries\GetElementRelations $elementRelationsQuery
    ) {
        $this->elementsQuery = $elementsQuery;
        $this->elementRelationsQuery = $elementRelationsQuery;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {

        try {
            //$elements = $this->elementsQuery->all();
            $elementRelations = $this->elementRelationsQuery->all();
            $data = $this->buildJsonRepresentation($elementRelations);
        } catch (Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 500);
        }

        return new JsonResponse($data);
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
