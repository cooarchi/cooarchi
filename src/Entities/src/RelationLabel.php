<?php
declare(strict_types=1);

namespace CooarchiEntities;

use CooarchiApp\ConfigProvider;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function filter_var;
use function mb_strlen;
use function trim;

/**
 * RelationLabel Entity
 *
 * @ORM\Entity()
 * @ORM\Table(
 *   name="relation_labels",
 *   indexes={
 *     @ORM\Index(name="relation_labels_descriptionx", columns={"description_text"})
 *   },
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="relation_labels_pub_idx", columns={"pub_id"})
 *   }
 * )
 */
class RelationLabel
{
    /**
     * @ORM\Column(type="uuid_binary_ordered_time", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @var string|Uuid
     */
    private $id;

    /**
     * @ORM\Column(name="pub_id", type="guid", nullable=false)
     * @var string
     */
    private $pubId;

    /**
     * @ORM\Column(name="description_text", type="string", nullable=false)
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(name="user", type="guid", nullable=true)
     * @var bool
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $created;

    public function __construct(UuidInterface $relationLabelId, string $description, ?string $userId)
    {
        if (mb_strlen($description, ConfigProvider::ENCODING) > 255) {
            throw new InvalidArgumentException('Description is too long (> 255 chars)');
        }
        if ($userId !== null && Uuid::isValid($userId) === false) {
            $userId = null;
        }

        $this->created = new DateTime('now', new DateTimeZone('UTC'));
        $this->id = $relationLabelId->toString();
        $this->description = trim((string) filter_var($description, FILTER_SANITIZE_STRING));
        $this->pubId = (string) Uuid::uuid4();
        $this->user = $userId;
    }

    public function getCreated() : DateTime
    {
        return $this->created;
    }

    public function getId() : UuidInterface
    {
        return $this->id;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function getPubId() : string
    {
        return $this->pubId;
    }

    public function getUser() : ?string
    {
        return $this->user;
    }
}
