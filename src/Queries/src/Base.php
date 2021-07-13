<?php
declare(strict_types=1);

namespace CooarchiQueries;

use Doctrine\ORM\EntityManager;

abstract class Base
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
