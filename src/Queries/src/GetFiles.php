<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;
use DateTime;

final class GetFiles extends Base
{
    /**
     * @return CooarchiEntities\File[]
     */
    public function all() : array
    {
        $query = $this->entityManager->createQuery(
            sprintf(
                'SELECT files FROM %s files ORDER BY files.created DESC',
                CooarchiEntities\File::class
            )
        );

        return $query->getResult();
    }
}
