<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiApp\Helper;
use CooarchiApp\ValueObject;
use CooarchiEntities;
use CooarchiQueries;
use Doctrine\ORM\EntityManager;
use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\UuidFactory;
use function filter_var;
use function trim;

class SaveHandler implements RequestHandlerInterface
{
    public const ROUTE = '/save';
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

    /**
     * @var UuidFactory
     */
    private $uuidFactory;

    public function __construct(
        EntityManager $entityManager,
        CooarchiQueries\FindElement $findElementQuery,
        CooarchiQueries\FindElementRelation $findElementRelationQuery,
        CooarchiQueries\FindRelationLabel $findRelationLabelQuery,
        UuidFactory $uuidFactory
    ) {
        $this->entityManager = $entityManager;
        $this->findElementQuery = $findElementQuery;
        $this->findElementRelationQuery = $findElementRelationQuery;
        $this->findRelationLabelQuery = $findRelationLabelQuery;
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
            $bodyAttributes = $request->getParsedBody();
            $uuidValidator = $this->uuidFactory->getValidator();

            $relationAttributes = $bodyAttributes['links'][0] ?? [];
            if ($relationAttributes === []) {
                return new JsonResponse(['error' => 'No relation/links data provided'], 500);
            }

            $elements = [];
            foreach ($bodyAttributes['nodes'] ?? [] as $elementData) {
                $elementValues = ValueObject\Element::createFromArray($elementData);
                $elementKey = $elementValues->getLabel();
                if ($elementValues->getElementId() !== null &&
                    $uuidValidator->validate($elementValues->getElementId()) === true
                ) {
                    $elementKey = $elementValues->getElementId();
                } elseif ($elementValues->getLabel() === null && $elementValues->getUrl() !== null) {
                    $elementKey = $elementValues->getUrl();
                }
                $elements[$elementKey] = $elementValues;
            }

            $sourceLabel = $relationAttributes['source']['label'];
            $targetLabel = $relationAttributes['target']['label'];
            $relationDescription = trim(
                (string) filter_var($relationAttributes['label'], FILTER_SANITIZE_STRING)
            );

            $sourceKey = $sourceLabel;
            if (isset($relationAttributes['source']['id']) === true &&
                $uuidValidator->validate($relationAttributes['source']['id']) === true
            ) {
                $sourceKey = $relationAttributes['source']['id'];
            } elseif (isset($relationAttributes['source']['isFile']) === true &&
                $relationAttributes['source']['isFile'] === true &&
                $relationAttributes['source']['url'] !== ''
            ) {
                $sourceKey = $relationAttributes['source']['url'];
            }
            $targetKey = $targetLabel;
            if (isset($relationAttributes['target']['id']) === true &&
                $uuidValidator->validate($relationAttributes['target']['id']) === true
            ) {
                $targetKey = $relationAttributes['target']['id'];
            } elseif (isset($relationAttributes['target']['isFile']) === true &&
                $relationAttributes['target']['isFile'] === true &&
                $relationAttributes['target']['url'] !== ''
            ) {
                $targetKey = $relationAttributes['target']['url'];
            }

            if (isset($elements[$sourceKey]) === false) {
                return new JsonResponse(['error' => 'Node data missing for source key: ' .  $sourceKey], 500);
            }
            if ($targetKey !== '' && isset($elements[$targetKey]) === false) {
                return new JsonResponse(['error' => 'Node data missing for target label: ' .  $targetKey], 500);
            }

            $elementFromValues = $elements[$sourceKey];
            $elementToValues = $elements[$targetKey] ?? null;

            $elementFrom = $this->findExistingElement($elementFromValues);

            // Element From only case
            if ($targetKey === null || $targetKey === '') {
                if ($elementFrom === null) {
                    $elementFrom = $this->createElement($elementFromValues);
                    $this->entityManager->persist($elementFrom);
                    $this->entityManager->flush();
                }
                return new JsonResponse(
                    Helper\JsonRepresentation::create([$elementFrom], []),
                    200
                );
            }

            $elementTo = $this->findExistingElement($elementToValues);

            $relationLabel = $this->findRelationLabelQuery->byDescription($relationDescription);
            $newElementCheck = false;

            if ($elementFrom === null) {
                $elementFrom = $this->createElement($elementFromValues);
                $this->entityManager->persist($elementFrom);
                $newElementCheck = true;
            }
            if ($sourceLabel === $targetLabel &&
                $elementFromValues->getUrl() === null && $elementToValues->getUrl() === null
            ) {
                $elementTo = $elementFrom;
            } elseif ($elementTo === null) {
                $elementTo = $this->createElement($elementToValues);
                $this->entityManager->persist($elementTo);
                $newElementCheck = true;
            }
            if ($relationLabel === null) {
                $relationLabel = new CooarchiEntities\RelationLabel($this->uuidFactory->uuid1(), $relationDescription);
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
                    Helper\JsonRepresentation::create([$elementFrom, $elementTo], [$relation]),
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
            Helper\JsonRepresentation::create([$elementFrom, $elementTo], [$elementRelation]),
            200
        );
    }

    private function createElement(ValueObject\Element $elementValues) : CooarchiEntities\Element
    {
        $isFile = false;
        if ($elementValues->getUrl() !== null) {
            $isFile = true;
        }

        return new CooarchiEntities\Element(
            $this->uuidFactory->uuid1(),
            $elementValues->isCoreElement(),
            $isFile,
            $elementValues->isLocation(),
            $elementValues->isLongText(),
            $elementValues->isTriggerWarning(),
            $elementValues->getLabel(),
            $elementValues->getLongText(),
            $elementValues->getUrl(),
            $elementValues->getMediaType()
        );
    }

    private function findExistingElement(ValueObject\Element $elementValues) : ?CooarchiEntities\Element
    {
        if ($elementValues->getElementId() !== null) {
            $element = $this->findElementQuery->byPubId($elementValues->getElementId());
        } elseif ($elementValues->getLabel() === null && $elementValues->getUrl() !== null) {
            $element = $this->findElementQuery->byFilePath($elementValues->getUrl());
        } else {
            $element = $this->findElementQuery->byInfo($elementValues->getLabel());
        }

        return $element;
    }
}
