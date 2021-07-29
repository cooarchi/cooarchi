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
use function in_array;
use function mb_strlen;
use function trim;

/**
 * File Entity
 *
 * @ORM\Entity()
 * @ORM\Table(
 *   name="file",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="efile_unique_pubidx", columns={"pub_id"})
 *   }
 * )
 */
class File
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
     * @ORM\Column(name="mime_type", type="string", nullable=false)
     * @var null|string
     */
    private $mimeType;

    /**
     * @ORM\Column(name="size", type="integer", nullable=false)
     * @var null|string
     */
    private $size;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $created;

    public function __construct(
        UuidInterface $fileId,
        string $mimeType,
        int $size,
        ?string $label
    ) {
        if ($label !== null && mb_strlen($label, ConfigProvider::ENCODING) > 255) {
            throw new InvalidArgumentException('Label Text is too long (> 255 chars)');
        }
        if ($label !== null) {
            $label = trim((string) filter_var($label, FILTER_SANITIZE_STRING));
        }

        $this->created = new DateTime('now', new DateTimeZone('UTC'));
        $this->id = $fileId->toString();
        $this->label = $label;
        $this->mimeType = trim((string) filter_var($mimeType, FILTER_SANITIZE_STRING));;
        $this->pubId = (string) Uuid::uuid4();
        $this->size = $size;
    }

    public function getCreated() : DateTime
    {
        return $this->created;
    }
    public function getId() : UuidInterface
    {
        return $this->id;
    }

    public function getLabel() : ?string
    {
        return $this->label;
    }

    public function getMimeType() : string
    {
        return $this->mimeType;
    }

    public function getPubId() : string
    {
        return $this->pubId;
    }

    public function getSize() : int
    {
        return $this->size;
    }
}
