<?php

declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;

final class FindUser extends Base
{
    public function byName(string $name) : ?CooarchiEntities\User
    {
        $query = $this->entityManager->createQuery("
            SELECT user FROM CooarchiEntities\User user WHERE user.name = :name
        ");
        $query->setParameter('name', $name);

        return $query->getOneOrNullResult();
    }
}
