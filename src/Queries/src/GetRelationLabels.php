<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;

final class GetRelationLabels extends Base
{
    /**
     * @return array|CooarchiEntities\RelationLabel[]
     */
    public function all() : array
    {
        $query = $this->entityManager->createQuery(
            sprintf(
                'SELECT relationLabels FROM %s relationLabels ORDER BY relationLabels.created DESC',
                CooarchiEntities\RelationLabel::class
            )
        );

        return $query->getResult();
    }
}
