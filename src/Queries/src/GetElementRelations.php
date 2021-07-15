<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;
use DateTime;

final class GetElementRelations extends Base
{
    /**
     * @return CooarchiEntities\ElementRelation[]
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

    /**
     * @param DateTime $since
     * @return CooarchiEntities\ElementRelation[]
     */
    public function delta(DateTime $since) : array
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
            WHERE elementRelations.created >= :since 
            ORDER BY elementRelations.created DESC
        ");

        $query->setParameter('since', $since);

        return $query->getArrayResult();
    }
}
