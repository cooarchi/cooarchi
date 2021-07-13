<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;

final class FindElement extends Base
{
    public function byInfo(string $infoText) : ?CooarchiEntities\Element
    {
        $query = $this->entityManager->createQuery("
            SELECT elements FROM CooarchiEntities\Element elements WHERE elements.info = :infoText
        ");
        $query->setParameter('infoText', $infoText);

        return $query->getOneOrNullResult();
    }
}
