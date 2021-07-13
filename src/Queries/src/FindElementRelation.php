<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;

final class FindElementRelation extends Base
{
    public function byKey(
        CooarchiEntities\Element $elementFrom,
        CooarchiEntities\Element $elementTo,
        CooarchiEntities\RelationLabel $relationLabel
    ) : ?CooarchiEntities\ElementRelation
    {
        $query = $this->entityManager->createQuery("
            SELECT elementRelation 
            FROM CooarchiEntities\ElementRelation elementRelation 
            WHERE elementRelation.elementFrom = :elementFrom
            AND elementRelation.elementTo = :elementTo
            AND elementRelation.relationLabel = :relationLabel
        ");

        $query->setParameter('elementFrom', $elementFrom->getId()->getBytes());
        $query->setParameter('elementTo', $elementTo->getId()->getBytes());
        $query->setParameter('relationLabel', $relationLabel->getId()->getBytes());

        return $query->getOneOrNullResult();
    }
}
