<?php

declare(strict_types=1);

namespace CooarchiApp\ValueObject;

use InvalidArgumentException;
use function is_string;
use function trim;

final class Element
{
    /**
     * @var null|string
     */
    private $elementId;

    /**
     * @var bool
     */
    private $isCoreElement;

    /**
     * @var bool
     */
    private $isLocation;

    /**
     * @var bool
     */
    private $isLongText;

    /**
     * @var null|string
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

    /**
     * @var null|string
     */
    private $url;

    private function __construct(
        bool $isCoreElement,
        bool $isLocation,
        bool $isLongText,
        ?string $longText,
        ?string $mediaType,
        ?string $label,
        bool $triggerWarning,
        ?string $url,
        ?string $elementId
    ) {
        $this->elementId = $elementId;
        $this->isCoreElement = $isCoreElement;
        $this->isLocation = $isLocation;
        $this->isLongText = $isLongText;
        $this->longText = $longText;
        $this->mediaType = $mediaType;
        $this->label = $label;
        $this->triggerWarning = $triggerWarning;
        $this->url = $url;
    }

    public static function createFromArray(array $values) : self
    {
        $url = $values['url'] ?? null;
        if (is_string($url) === true) {
            $url = trim($url);
            if ($url === '') {
                $url = null;
            }
        }

        $label = $values['label'] ?? null;
        if ($label === '') {
            $label = null;
        }

        if ($url === null && $label === '') {
            throw new InvalidArgumentException('Label missing for node/element');
        }

        $mediaType = $values['mediaType'] ?? null;
        if ($mediaType === \CooarchiEntities\Element::MEDIA_TYPE_NONE) {
            $mediaType = null;
        }

        $elementId = $values['id'] ?? null;

        return new self(
            $values['isCoreElement'] ?? false,
            $values['isLocation'] ?? false,
            $values['isLongText'] ?? false,
            $values['longText'] ?? null,
            $mediaType,
            $label,
            $values['triggerWarning'] ?? false,
            $url,
            $elementId
        );
    }

    public function isCoreElement() : bool
    {
        return $this->isCoreElement;
    }

    public function isLocation() : bool
    {
        return $this->isLocation;
    }

    public function isLongText() : bool
    {
        return $this->isLongText;
    }

    public function getElementId() : ?string
    {
        return $this->elementId;
    }

    public function getLongText() : ?string
    {
        return $this->longText;
    }

    public function getMediaType() : ?string
    {
        return $this->mediaType;
    }

    public function getLabel() : ?string
    {
        return $this->label;
    }

    public function getUrl() : ?string
    {
        return $this->url;
    }

    public function isTriggerWarning() : bool
    {
        return $this->triggerWarning;
    }
}
