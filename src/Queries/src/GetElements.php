<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;

final class GetElements extends Base
{
    /**
     * @return array|CooarchiEntities\Element[]
     */
    public function all() : array
    {
        $query = $this->entityManager->createQuery(
            sprintf(
                'SELECT elements FROM %s elements ORDER BY elements.created DESC',
                CooarchiEntities\Element::class
            )
        );

        return $query->getResult();
    }
}
