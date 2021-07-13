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

class SaveHandler implements RequestHandlerInterface
{
    public const ROUTE_NAME = 'save';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CooarchiQueries\FindElement
     */
    private $findElementQuery;

    /**
     * @var CooarchiQueries\FindElementRelation
     */
    private $findElementRelationQuery;

    /**
     * @var CooarchiQueries\FindRelationLabel
     */
    private $findRelationLabelQuery;

    public function __construct(
        EntityManager $entityManager,
        CooarchiQueries\FindElement $findElementQuery,
        CooarchiQueries\FindElementRelation $findElementRelationQuery,
        CooarchiQueries\FindRelationLabel $findRelationLabelQuery
    ) {
        $this->entityManager = $entityManager;
        $this->findElementQuery = $findElementQuery;
        $this->findElementRelationQuery = $findElementRelationQuery;
        $this->findRelationLabelQuery = $findRelationLabelQuery;
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
            $bodyContent= $request->getBody()->getContents();
            $bodyAttributes = Json::decode($bodyContent, Json::TYPE_ARRAY);

            if (isset($bodyAttributes['source']['id']) === false &&
                isset($bodyAttributes['target']['id']) === false &&
                isset($bodyAttributes['name']) === false
            ) {
                return new JsonResponse(['error' => 'Missing input'], 500);
            }

            /** @var UuidFactory $uuidFactory */
            $uuidFactory = Uuid::getFactory();
            $codec = new OrderedTimeCodec($uuidFactory->getUuidBuilder());
            $uuidFactory->setCodec($codec);

            $elementFromText = trim((string) filter_var($bodyAttributes['source']['id'], FILTER_SANITIZE_STRING));
            $elementToText = trim((string) filter_var($bodyAttributes['target']['id'], FILTER_SANITIZE_STRING));
            $relationText = trim((string) filter_var($bodyAttributes['name'], FILTER_SANITIZE_STRING));

            $elementFrom = $this->findElementQuery->byInfo($elementFromText);
            $elementTo = $this->findElementQuery->byInfo($elementToText);
            $relationLabel = $this->findRelationLabelQuery->byDescription($relationText);

            // Element From only case
            if ($elementFromText === '') {
                if ($elementFrom === null) {
                    $elementFrom = new CooarchiEntities\Element($uuidFactory->uuid1(), false, $elementFromText);
                    $this->entityManager->persist($elementFrom);
                    $this->entityManager->flush();
                }
                return new JsonResponse(
                    [
                        'message' => 'Core element created',
                        'element' => [
                            'id' => $elementFrom->getPubId(),
                            'name' => $elementFrom->getInfo()
                        ],
                    ],
                    200
                );
            }

            $newElementCheck = false;
            if ($elementFrom === null) {
                $elementFrom = new CooarchiEntities\Element($uuidFactory->uuid1(), false, $elementFromText);
                $this->entityManager->persist($elementFrom);
                $newElementCheck = true;
            }
            if ($elementFromText === $elementToText) {
                $elementTo = $elementFrom;
            }
            elseif ($elementTo === null) {
                $elementTo = new CooarchiEntities\Element($uuidFactory->uuid1(), false, $elementToText);
                $this->entityManager->persist($elementTo);
                $newElementCheck = true;
            }
            if ($relationLabel === null) {
                $relationLabel = new CooarchiEntities\RelationLabel($uuidFactory->uuid1(), $relationText);
                $this->entityManager->persist($relationLabel);
                $newElementCheck = true;
            }

            if ($newElementCheck === true) {
                $this->entityManager->flush();
                $this->entityManager->refresh($elementFrom);
                $this->entityManager->refresh($elementTo);
                $this->entityManager->refresh($relationLabel);
            }

            $relation = $this->findElementRelationQuery->byKey($elementFrom, $elementTo, $relationLabel);

            if ($relation !== null) {
                return new JsonResponse(
                    [
                        'name' => $relation->getRelationLabel()->getDescription(),
                        'source' => [
                            'id' => $relation->getElementFrom()->getPubId(),
                            'name' => $relation->getElementFrom()->getInfo(),
                        ],
                        'target' => [
                            'id' => $relation->getElementTo()->getPubId(),
                            'name' => $relation->getElementTo()->getInfo(),
                        ],
                    ],
                    200
                );
            }

            $elementRelation = new CooarchiEntities\ElementRelation(
                $elementFrom,
                $elementTo,
                $relationLabel
            );
            $this->entityManager->persist($elementRelation);

            $this->entityManager->flush();
        } catch (Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 500);
        }

        return new JsonResponse(
            [
                'name' => $relationLabel->getDescription(),
                'source' => [
                    'id' => $elementFrom->getPubId(),
                    'name' => $elementFrom->getInfo(),
                ],
                'target' => [
                    'id' => $elementTo->getPubId(),
                    'name' => $elementTo->getInfo(),
                ],
            ],
            200
        );
    }
}
