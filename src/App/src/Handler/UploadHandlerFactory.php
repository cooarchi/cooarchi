<?php
declare(strict_types=1);

namespace CooarchiApp\Handler;

use LogicException;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;

class UploadHandlerFactory
{
    public function __invoke(ContainerInterface $container) : UploadHandler
    {
        $entityManager = $container->get('doctrine.entity_manager.orm_default');

        $basePath = $container->get('config')['basePath'] ?? null;
        if ($basePath === null) {
            throw new LogicException('basePath config value is missing');
        }

        /** @var UuidFactory $uuidFactory */
        $uuidFactory = Uuid::getFactory();
        $codec = new OrderedTimeCodec($uuidFactory->getUuidBuilder());
        $uuidFactory->setCodec($codec);

        return new UploadHandler(
            $entityManager,
            $uuidFactory,
            $basePath
        );
    }
}
