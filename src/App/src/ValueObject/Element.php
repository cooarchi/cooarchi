<?php

declare(strict_types=1);

namespace CooarchiApp\ValueObject;


use InvalidArgumentException;

final class Element
{
    /**
     * @var bool
     */
    private $isLocation;

    /**
     * @var bool
     */
    private $isLongText;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $longText;

    /**
     * @var null|string
     */
    private $mediaType;

    /**
     * @var bool
     */
    private $triggerWarning;

    private function __construct(
        bool $isLocation,
        bool $isLongText,
        ?string $longText,
        ?string $mediaType,
        string $label,
        bool $triggerWarning
    ) {
        $this->isLocation = $isLocation;
        $this->isLongText = $isLongText;
        $this->longText = $longText;
        $this->mediaType = $mediaType;
        $this->label = $label;
        $this->triggerWarning = $triggerWarning;
    }

    public static function createFromArray(array $values) : self
    {
        if (isset($values['label']) === false || $values['label'] === '') {
            throw new InvalidArgumentException('Label missing for node/element');
        }

        $mediaType = $values['mediaType'] ?? null;
        if ($mediaType === \CooarchiEntities\Element::MEDIA_TYPE_NONE) {
            $mediaType = null;
        }

        return new self(
            $values['isLocation'] ?? false,
            $values['isLongText'] ?? false,
            $values['longText'] ?? null,
            $mediaType,
            $values['label'],
            $values['triggerWarning'] ?? false
        );
    }

    public function isLocation() : bool
    {
        return $this->isLocation;
    }

    public function isLongText() : bool
    {
        return $this->isLongText;
    }

    public function getLongText() : ?string
    {
        return $this->longText;
    }

    public function getMediaType() : ?string
    {
        return $this->mediaType;
    }

    public function getLabel() : string
    {
        return $this->label;
    }

    public function isTriggerWarning() : bool
    {
        return $this->triggerWarning;
    }
}
