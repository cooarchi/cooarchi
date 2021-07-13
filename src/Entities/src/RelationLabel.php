<?php
declare(strict_types=1);

namespace CooarchiEntities;

use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function filter_var;

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
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $created;

    public function __construct(UuidInterface $relationLabelId, string $description)
    {
        $this->created = new DateTime('now', new DateTimeZone('UTC'));
        $this->id = $relationLabelId->toString();
        $this->description = filter_var($description, FILTER_SANITIZE_STRING);
        $this->pubId = (string) Uuid::uuid4();
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
}
