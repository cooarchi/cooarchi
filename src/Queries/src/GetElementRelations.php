<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;

final class GetElementRelations extends Base
{
    /**
     * @return array|CooarchiEntities\ElementRelation[]
     */
    public function all() : array
    {
        $query = $this->entityManager->createQuery("
            SELECT 
                relationLabels.description as relationLabel, 
                elementFrom.pubId as pubIdFrom,
                elementTo.pubId as pubIdTo
            FROM CooarchiEntities\ElementRelation elementRelations
            LEFT JOIN elementRelations.relationLabel relationLabels
            LEFT JOIN elementRelations.elementTo elementTo
            LEFT JOIN elementRelations.elementFrom elementFrom
            ORDER BY elementRelations.created DESC
        ");

        return $query->getArrayResult();
    }
}
