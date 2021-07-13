<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;
use Doctrine\DBAL\Types\Types;

final class FindRelationLabel extends Base
{
    public function byDescription(string $descriptionText) : ?CooarchiEntities\RelationLabel
    {
        $query = $this->entityManager->createQuery("
            SELECT relationLabel 
            FROM CooarchiEntities\RelationLabel relationLabel 
            WHERE relationLabel.description = :descriptionText
        ");
        $query->setParameter('descriptionText', $descriptionText, Types::STRING);

        return $query->getOneOrNullResult();
    }
}
