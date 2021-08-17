<?php
declare(strict_types=1);

namespace CooarchiQueries;

use CooarchiEntities;
use DateTime;

final class GetInvitations extends Base
{
    /**
     * @return CooarchiEntities\Invitation[]
     */
    public function all() : array
    {
        $query = $this->entityManager->createQuery(
            sprintf(
                'SELECT invitations FROM %s invitations ORDER BY invitations.created DESC',
                CooarchiEntities\Invitation::class
            )
        );

        return $query->getResult();
    }
}
