<?php

declare(strict_types=1);

namespace CooarchiEntities;

use CooarchiApp\ConfigProvider;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use function bin2hex;
use function random_bytes;
use function substr;

/**
 * Invitation Entity
 *
 * @ORM\Entity()
 * @ORM\Table(
 *   name="invitations",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="invitations_unique_identifierx", columns={"identifier"}),
 *     @ORM\UniqueConstraint(name="invitations_unique_hashx", columns={"hash"})
 *   }
 * )
 */
class Invitation
{
    private const HASH_LENGTH = 20;

    /**
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(name="identifier", type="string", length=100, nullable=false)
     * @var string
     */
    private $identifier;

    /**
     * @ORM\Column(name="hash", type="string", length=20, nullable=false)
     * @var string
     */
    private $hash;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $created;

    public function __construct(
        string $identifier
    ) {
        if (mb_strlen($identifier, ConfigProvider::ENCODING) > 100) {
            throw new InvalidArgumentException('identifier text is too long (> 240 chars)');
        }

        $this->id = (string) Uuid::uuid4();
        $this->identifier = $identifier;
        $this->hash = $this->generateRandomString();
        $this->created = new DateTime('now', new DateTimeZone('UTC'));
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    private function generateRandomString() : string
    {
        return (string) substr(
            bin2hex(random_bytes(self::HASH_LENGTH)),
            0,
            self::HASH_LENGTH
        );
    }
}
