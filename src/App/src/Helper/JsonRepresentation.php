<?php

declare(strict_types=1);

namespace CooarchiApp\Helper;

use CooarchiEntities;

final class JsonRepresentation
{
    public static function create(array $elements, array $relations) : array
    {
        $json = [
            'nodes' => [],
            'links' => [],
        ];

        /** @var CooarchiEntities\Element $element */
        foreach ($elements as $element) {
            $node = [
                'id' => $element->getPubId(),
                'label' => $element->getLabel(),
                'isCoreElement' => $element->isCoreElement(),
                'isFile' => $element->isFile(),
                'isLocation' => $element->isLocation(),
                'isLongText' => $element->isLongText(),
                'longText' => $element->getLongText(),
                'mediaType' => $element->getMediaType(),
                'triggerWarning' => $element->hasTriggerWarning(),
                'url' => $element->getUrl(),
            ];
            $json['nodes'][] = $node;
        }

        /** @var CooarchiEntities\ElementRelation|array $relation */
        foreach ($relations as $relation) {
            if ($relation instanceof CooarchiEntities\ElementRelation) {
                $relation = [
                    'relationLabel' => $relation->getRelationLabel()->getDescription(),
                    'pubIdFrom' => $relation->getElementFrom()->getPubId(),
                    'pubIdTo' => $relation->getElementTo()->getPubId(),
                ];
            }

            $json['links'][] = [
                'label' => $relation['relationLabel'],
                'source' => [
                    'id' => $relation['pubIdFrom'],
                ],
                'target' => [
                    'id' => $relation['pubIdTo'],
                ],
            ];
        }

        return $json;
    }
}
