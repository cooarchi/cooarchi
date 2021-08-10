<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;

final class FindElement extends Base
{
    public function byFilePath(string $filePath) : ?CooarchiEntities\Element
    {
        $query = $this->entityManager->createQuery("
            SELECT elements FROM CooarchiEntities\Element elements WHERE elements.filePath = :filePath
        ");
        $query->setParameter('filePath', $filePath);

        return $query->getOneOrNullResult();
    }

    public function byInfo(string $labelText) : ?CooarchiEntities\Element
    {
        $query = $this->entityManager->createQuery("
            SELECT elements FROM CooarchiEntities\Element elements WHERE elements.label = :labelText
        ");
        $query->setParameter('labelText', $labelText);

        return $query->getOneOrNullResult();
    }

    public function byLongText(string $longText) : ?CooarchiEntities\Element
    {
        $query = $this->entityManager->createQuery("
            SELECT elements FROM CooarchiEntities\Element elements WHERE elements.longText = :longText
        ");
        $query->setParameter('longText', $longText);

        return $query->getOneOrNullResult();
    }

    public function byPubId(string $pubId) : ?CooarchiEntities\Element
    {
        $query = $this->entityManager->createQuery("
            SELECT elements FROM CooarchiEntities\Element elements WHERE elements.pubId = :pubId
        ");
        $query->setParameter('pubId', $pubId);

        return $query->getOneOrNullResult();
    }
}
