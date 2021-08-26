<?php

declare(strict_types=1);

namespace CooarchiEntities;

use CooarchiApp\ConfigProvider;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

/**
 * User Entity
 *
 * @ORM\Entity()
 * @ORM\Table(
 *   name="users",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="users_unique_namex", columns={"name"})
 *   }
 * )
 */
class User
{
    public const ROLE_ADMINISTRATA = 'administrata';
    public const ROLE_KOLLEKTIVISTA = 'kollektivista';
    public const ROLE_TRAVELLA = 'travella';

    /**
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(name="password", type="string", length=128, nullable=false)
     * @var string
     */
    private $password;

    /**
     * @ORM\Column(name="name", type="string", length=240, nullable=false)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="role", type="string", length=20, nullable=false)
     * @var string
     */
    private $role;

    /**
     * @ORM\Column(name="invitation_hash", type="string", length=20, nullable=true)
     * @var string
     */
    private $invitationHash;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $created;

    public function __construct(
        string $name,
        string $password,
        string $role = self::ROLE_TRAVELLA,
        ?string $invitationHash = null
    ) {
        if (mb_strlen($name, ConfigProvider::ENCODING) > 240) {
            throw new InvalidArgumentException('name text is too long (> 240 chars)');
        }
        if (mb_strlen($name, ConfigProvider::ENCODING) > 128) {
            throw new InvalidArgumentException('password text is too long (> 128 chars)');
        }
        if ($invitationHash !== null && mb_strlen($invitationHash, ConfigProvider::ENCODING) > 20) {
            throw new InvalidArgumentException('invitationHash text is too long (> 20 chars)');
        }
        if ($role !== self::ROLE_ADMINISTRATA &&
            $role !== self::ROLE_KOLLEKTIVISTA &&
            $role !== self::ROLE_TRAVELLA
        ) {
            throw new InvalidArgumentException('provided role is invalid');
        }

        $this->id = (string) Uuid::uuid4();
        $this->invitationHash= $invitationHash;
        $this->name = $name;
        $this->password = $password;
        $this->role = $role;
        $this->created = new DateTime('now', new DateTimeZone('UTC'));
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getInvitationHash() : ?string
    {
        return $this->invitationHash;
    }

    public function getPassword() : string
    {
        return $this->password;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getRole() : string
    {
        return $this->role;
    }

    public function getCreated() : DateTime
    {
        return $this->created;
    }
}
