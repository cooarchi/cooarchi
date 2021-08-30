<?php
declare(strict_types=1);

namespace CooarchiEntities;

use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * ElementRelation Entity
 *
 * @ORM\Entity()
 * @ORM\Table(
 *   name="element_relations",
 *   indexes={
 *     @ORM\Index(name="element_relation_from_idx", columns={"element_from_id"}),
 *     @ORM\Index(name="element_relation_to_idx", columns={"element_to_id"}),
 *     @ORM\Index(name="element_relation_label_idx", columns={"relation_label_id"})
 *   }
 * )
 */
class ElementRelation
{
    /**
     * @ORM\ManyToOne(targetEntity="Element")
     * @ORM\JoinColumn(name="element_from_id", referencedColumnName="id", nullable=false)
     * @ORM\Id()
     * @var Element
     */
    private $elementFrom;

    /**
     * @ORM\ManyToOne(targetEntity="Element")
     * @ORM\JoinColumn(name="element_to_id", referencedColumnName="id", nullable=false)
     * @ORM\Id()
     * @var Element
     */
    private $elementTo;

    /**
     * @ORM\ManyToOne(targetEntity="RelationLabel")
     * @ORM\JoinColumn(name="relation_label_id", referencedColumnName="id", nullable=false)
     * @ORM\Id()
     * @var RelationLabel
     */
    private $relationLabel;

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

    public function __construct(
        Element $elementFrom,
        Element $elementTo,
        RelationLabel $relationLabel,
        ?string $userId
    ) {
        if ($userId !== null && Uuid::isValid($userId) === false) {
            $userId = null;
        }

        $this->created = new DateTime('now', new DateTimeZone('UTC'));
        $this->elementFrom = $elementFrom;
        $this->elementTo = $elementTo;
        $this->relationLabel = $relationLabel;
        $this->user = $userId;
    }

    public function getCreated() : DateTime
    {
        return $this->created;
    }

    public function getElementFrom() : Element
    {
        return $this->elementFrom;
    }

    public function getElementTo() : Element
    {
        return $this->elementTo;
    }

    public function getRelationLabel() : RelationLabel
    {
        return $this->relationLabel;
    }

    public function getUser() : ?string
    {
        return $this->user;
    }
}
