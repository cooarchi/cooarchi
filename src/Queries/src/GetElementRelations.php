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
            SELECT elementRelations
            FROM CooarchiEntities\ElementRelation elementRelations
            ORDER BY elementRelations.created DESC
        ");
        //LEFT JOIN elementRelations.RelationLabel relationLabels
        //LEFT JOIN elementRelations.Element elements

        return $query->getResult();
    }
}
