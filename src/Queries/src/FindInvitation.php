<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;

final class FindInvitation extends Base
{
    public function byIdentifier(string $identifier) : ?CooarchiEntities\Invitation
    {
        $query = $this->entityManager->createQuery("
            SELECT invitation FROM CooarchiEntities\Invitation invitation WHERE invitation.identifier = :identifier
        ");
        $query->setParameter('identifier', $identifier);

        return $query->getOneOrNullResult();
    }

    public function byHash(string $hash) : ?CooarchiEntities\Invitation
    {
        $query = $this->entityManager->createQuery("
            SELECT invitation FROM CooarchiEntities\Invitation invitation WHERE invitation.hash = :hash
        ");
        $query->setParameter('hash', $hash);

        return $query->getOneOrNullResult();
    }

    public function byId(string $invitationId) : ?CooarchiEntities\Invitation
    {
        $query = $this->entityManager->createQuery("
            SELECT invitation FROM CooarchiEntities\Invitation invitation WHERE invitation.id = :invitationId
        ");
        $query->setParameter('invitationId', $invitationId);

        return $query->getOneOrNullResult();
    }
}
