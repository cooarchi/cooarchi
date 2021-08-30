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
use Ramsey\Uuid\Validator\ValidatorInterface;
use function filter_var;
use function trim;

class SaveHandler implements RequestHandlerInterface
{
    public const ROUTE = '/save';
    public const ROUTE_NAME = 'save';

    private const RELATION_TYPE_SOURCE = 'source';
    private const RELATION_TYPE_TARGET = 'target';

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

    /**
     * @var ValidatorInterface
     */
    private $uuidValidator;

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
        $this->uuidValidator = $uuidFactory->getValidator();
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

        /** @var CooarchiEntities\User $user */
        $user = $request->getAttribute('identity', false);

        try {
            $bodyAttributes = $request->getParsedBody();

            $relationAttributes = $bodyAttributes['links'][0] ?? [];
            if ($relationAttributes === []) {
                return new JsonResponse(['error' => 'No relation/links data provided'], 500);
            }

            $elements = [];
            foreach ($bodyAttributes['nodes'] ?? [] as $elementData) {
                $elementValues = ValueObject\Element::createFromArray($elementData);
                $elementKey = $elementValues->getLabel();
                if ($elementValues->getElementId() !== null &&
                    $this->uuidValidator->validate($elementValues->getElementId()) === true
                ) {
                    $elementKey = $elementValues->getElementId();
                } elseif ($elementValues->getLabel() === null && $elementValues->getUrl() !== null) {
                    $elementKey = $elementValues->getUrl();
                }
                $elements[$elementKey] = $elementValues;
            }

            $sourceLabel = $relationAttributes['source']['label'];
            $targetLabel = $relationAttributes['target']['label'];

            $sourceKey = $this->getElementKey($relationAttributes, $sourceLabel, self::RELATION_TYPE_SOURCE);
            $targetKey = $this->getElementKey($relationAttributes, $targetLabel, self::RELATION_TYPE_TARGET);

            $relationDescription = trim(
                (string) filter_var($relationAttributes['label'], FILTER_SANITIZE_STRING)
            );

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
                    $elementFrom = $this->createElement($elementFromValues, $user);
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
                $elementFrom = $this->createElement($elementFromValues, $user);
                $this->entityManager->persist($elementFrom);
                $newElementCheck = true;
            }
            if ($sourceLabel === $targetLabel &&
                $elementFromValues->getUrl() === null && $elementToValues->getUrl() === null
            ) {
                $elementTo = $elementFrom;
            } elseif ($elementTo === null) {
                $elementTo = $this->createElement($elementToValues, $user);
                $this->entityManager->persist($elementTo);
                $newElementCheck = true;
            }
            if ($relationLabel === null) {
                $relationLabel = new CooarchiEntities\RelationLabel(
                    $this->uuidFactory->uuid1(),
                    $relationDescription,
                    $user->getId()
                );
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
                $relationLabel,
                $user->getId()
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

    private function createElement(
        ValueObject\Element $elementValues,
        CooarchiEntities\User $user
    ) : CooarchiEntities\Element
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
            $elementValues->getMediaType(),
            $user->getId()
        );
    }

    private function findExistingElement(ValueObject\Element $elementValues) : ?CooarchiEntities\Element
    {
        $elementId = $elementValues->getElementId();
        $label = $elementValues->getLabel();
        $longText = $elementValues->getLongText();
        $url = $elementValues->getUrl();

        if ($elementId !== null) {
            $element = $this->findElementQuery->byPubId($elementId);
        } elseif ($label === null && $url !== null) {
            $element = $this->findElementQuery->byFilePath($url);
        } elseif ($label === null && $url === null && $longText !== null) {
            $element = $this->findElementQuery->byLongText($longText);
        } else {
            $element = $this->findElementQuery->byInfo($label);
        }

        return $element;
    }

    private function getElementKey(array $relationAttributes, ?string $label, string $type) : ?string
    {
        if (isset($relationAttributes[$type]['id']) === true &&
            $this->uuidValidator->validate($relationAttributes[$type]['id']) === true
        ) {
            return $relationAttributes[$type]['id'];
        }

        if (isset($relationAttributes[$type]['isFile']) === true &&
            $relationAttributes[$type]['isFile'] === true &&
            $relationAttributes[$type]['url'] !== ''
        ) {
            return $relationAttributes[$type]['url'];
        }

        if (isset($relationAttributes[$type]['isLongText']) === true &&
            $relationAttributes[$type]['isLongText'] === true &&
            $relationAttributes[$type]['longText'] !== ''
        ) {
            return $relationAttributes[$type]['longText'];
        }

        return $label;
    }
}
