<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;

final class FindElement extends Base
{
    public function byInfo(string $labelText) : ?CooarchiEntities\Element
    {
        $query = $this->entityManager->createQuery("
            SELECT elements FROM CooarchiEntities\Element elements WHERE elements.label = :labelText
        ");
        $query->setParameter('labelText', $labelText);

        return $query->getOneOrNullResult();
    }
}
