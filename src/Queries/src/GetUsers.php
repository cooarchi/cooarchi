<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;
use DateTime;

final class GetUsers extends Base
{
    /**
     * @return CooarchiEntities\User[]
     */
    public function all() : array
    {
        $query = $this->entityManager->createQuery(
            sprintf(
                'SELECT users FROM %s users ORDER BY users.name ASC',
                CooarchiEntities\User::class
            )
        );

        return $query->getResult();
    }
}
