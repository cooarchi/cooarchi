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
    public const MEDIA_TYPE_NONE = 'none';
    public const MEDIA_TYPE_IMAGE = 'image';
    public const MEDIA_TYPE_AUDIO = 'audio';
    public const MEDIA_TYPE_VIDEO = 'audio';

    public const MEDIA_TYPES = [
        self::MEDIA_TYPE_AUDIO,
        self::MEDIA_TYPE_IMAGE,
        self::MEDIA_TYPE_VIDEO,
    ];

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
     * @ORM\Column(name="is_core_element", type="boolean", nullable=false)
     * @var bool
     */
    private $isCoreElement;

    /**
     * @ORM\Column(name="is_file", type="boolean", nullable=false)
     * @var bool
     */
    private $isFile;

    /**
     * @ORM\Column(name="is_location", type="boolean", nullable=false)
     * @var bool
     */
    private $isLocation;

    /**
     * @ORM\Column(name="is_long_text", type="boolean", nullable=false)
     * @var bool
     */
    private $isLongText;

    /**
     * @ORM\Column(name="long_text", type="text", nullable=true)
     * @var string
     */
    private $longText;

    /**
     * @ORM\Column(name="file_path", type="string", nullable=true)
     * @var null|string
     */
    private $filePath;

    /**
     * @ORM\Column(name="media_type", type="string", length=10, nullable=true)
     * @var null|string
     */
    private $mediaType;

    /**
     * @ORM\Column(name="trigger_warning", type="boolean", nullable=false)
     * @var bool
     */
    private $triggerWarning;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $created;

    public function __construct(
        UuidInterface $elementId,
        bool $isCoreElement,
        bool $isFile,
        bool $isLocation,
        bool $isLongText,
        bool $triggerWarning,
        ?string $label,
        ?string $longText = null,
        ?string $filePath = null,
        ?string $mediaType = null
    ) {
        if ($label !== null && mb_strlen($label, ConfigProvider::ENCODING) > 255) {
            throw new InvalidArgumentException('Info Text is too long (> 255 chars)');
        }
        if ($mediaType !== null && in_array($mediaType, self::MEDIA_TYPES, true) === false) {
            throw new InvalidArgumentException('Provided MediaType attribute is invalid');
        }

        if ($label !== null) {
            $label = trim((string) filter_var($label, FILTER_SANITIZE_STRING));
        }
        if ($longText !== null) {
            $longText = trim((string) filter_var($longText, FILTER_SANITIZE_STRING));
        }

        $this->created = new DateTime('now', new DateTimeZone('UTC'));
        $this->filePath = $filePath;
        $this->id = $elementId->toString();
        $this->label = $label;
        $this->isCoreElement = $isCoreElement;
        $this->isFile = $isFile;
        $this->isLocation = $isLocation;
        $this->isLongText = $isLongText;
        $this->longText = $longText;
        $this->mediaType = $mediaType;
        $this->pubId = (string) Uuid::uuid4();
        $this->triggerWarning = $triggerWarning;
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

    public function getLabel() : ?string
    {
        return $this->label;
    }

    public function getLongText() : ?string
    {
        return $this->longText;
    }

    public function getMediaType() : ?string
    {
        return $this->mediaType;
    }

    public function getPubId() : string
    {
        return $this->pubId;
    }

    public function hasTriggerWarning() : bool
    {
        return $this->triggerWarning;
    }

    public function isCoreElement() : bool
    {
        return $this->isCoreElement;
    }

    public function isFile() : bool
    {
        return $this->isFile;
    }

    public function isLocation() : bool
    {
        return $this->isLocation;
    }

    public function isLongText() : bool
    {
        return $this->isLongText;
    }
}
