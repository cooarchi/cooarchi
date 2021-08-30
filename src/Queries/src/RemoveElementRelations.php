<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;

final class RemoveElementRelations extends Base
{
    public function byElement(
        CooarchiEntities\Element $element
    ) : int
    {
        $query = $this->entityManager->createQuery("
            DELETE CooarchiEntities\ElementRelation elementRelation 
            WHERE elementRelation.elementFrom = :element
            OR elementRelation.elementTo = :element
        ");

        $query->setParameter('element', $element->getId()->getBytes());

        return $query->execute();
    }
}
