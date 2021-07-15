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
 * Element Entity
 *
 * @ORM\Entity()
 * @ORM\Table(
 *   name="elements",
 *   indexes={
 *     @ORM\Index(name="element_infox", columns={"info"})
 *   },
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="element_unique_pubidx", columns={"pub_id"})
 *   }
 * )
 */
class Element
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
     * @ORM\Column(name="label", type="string", nullable=true)
     * @var null|string
     */
    private $label;

    /**
     * @ORM\Column(name="is_file", type="boolean", nullable=false)
     * @var string
     */
    private $isFile;

    /**
     * @ORM\Column(name="file_path", type="string", nullable=true)
     * @var null|string
     */
    private $filePath;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $created;

    public function __construct(UuidInterface $elementId, bool $isFile, ?string $info, ?string $filePath = null)
    {
        $this->created = new DateTime('now', new DateTimeZone('UTC'));
        $this->filePath = $filePath;
        $this->id = $elementId->toString();
        $this->info = filter_var($info, FILTER_SANITIZE_STRING);
        $this->isFile = $isFile;
        $this->pubId = (string) Uuid::uuid4();
    }

    public function getCreated() : DateTime
    {
        return $this->created;
    }

    public function getFilePath() : ?string
    {
        return $this->filePath;
    }

    public function getId() : UuidInterface
    {
        return $this->id;
    }

    public function getInfo() : ?string
    {
        return $this->info;
    }

    public function getPubId() : string
    {
        return $this->pubId;
    }

    public function isFile() : bool
    {
        return $this->isFile;
    }
}
