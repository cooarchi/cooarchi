<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;

final class FindFile extends Base
{
    public function byPubId(string $pubId) : ?CooarchiEntities\File
    {
        $query = $this->entityManager->createQuery("
            SELECT files FROM CooarchiEntities\File files WHERE files.pubId = :pubId
        ");
        $query->setParameter('pubId', $pubId);

        return $query->getOneOrNullResult();
    }
}
