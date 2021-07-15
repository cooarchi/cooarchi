<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;
use DateTime;

final class GetElements extends Base
{
    /**
     * @return CooarchiEntities\Element[]
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

    /**
     * @param DateTime $since
     * @return CooarchiEntities\Element[]
     */
    public function delta(DateTime $since) : array
    {
        $query = $this->entityManager->createQuery(
            sprintf(
                'SELECT elements FROM %s elements WHERE elements.created >= :since ORDER BY elements.created DESC',
                CooarchiEntities\Element::class
            )
        );

        $query->setParameter('since', $since);

        return $query->getResult();
    }
}
