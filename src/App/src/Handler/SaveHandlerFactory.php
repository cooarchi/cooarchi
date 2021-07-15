<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiQueries;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;

class SaveHandlerFactory
{
    public function __invoke(ContainerInterface $container) : SaveHandler
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');

         /** @var UuidFactory $uuidFactory */
        $uuidFactory = Uuid::getFactory();
        $codec = new OrderedTimeCodec($uuidFactory->getUuidBuilder());
        $uuidFactory->setCodec($codec);

        return new SaveHandler(
            $entityManager,
            new CooarchiQueries\FindElement($entityManager),
            new CooarchiQueries\FindElementRelation($entityManager),
            new CooarchiQueries\FindRelationLabel($entityManager),
            $uuidFactory
        );
    }
}
