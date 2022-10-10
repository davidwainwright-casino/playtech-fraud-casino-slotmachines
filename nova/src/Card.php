<?php

namespace Laravel\Nova;

abstract class Card extends Element
{
    public const FULL_WIDTH = 'full';

    public const ONE_THIRD_WIDTH = '1/3';

    public const ONE_HALF_WIDTH = '1/2';

    public const ONE_QUARTER_WIDTH = '1/4';

    public const TWO_THIRDS_WIDTH = '2/3';

    public const THREE_QUARTERS_WIDTH = '3/4';

    public const FIXED_HEIGHT = 'fixed';

    public const DYNAMIC_HEIGHT = 'dynamic';

    /**
     * The width of the card (1/3, 2/3, 1/2, 1/4, 3/4, or full).
     *
     * @var string
     */
    public $width = '1/3';

    /**
     * The height strategy of the card.
     *
     * @var string
     */
    public $height = 'fixed';

    /**
     * Set the width of the card.
     *
     * @param  string  $width
     * @return $this
     */
    public function width($width)
    {
        $this->width = $width;

        if ($this->width == static::FULL_WIDTH) {
            $this->height = static::DYNAMIC_HEIGHT;
        }

        return $this;
    }

    /**
     * Set the height of a card to use a fixed value.
     *
     * @param  string  $height
     * @return $this
     */
    public function height($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Set the height of a card to be dynamic.
     *
     * @return $this
     */
    public function dynamicHeight()
    {
        $this->height = static::DYNAMIC_HEIGHT;

        return $this;
    }

    /**
     * Set the height of a card to be fixed.
     *
     * @return $this
     */
    public function fixedHeight()
    {
        $this->height = static::FIXED_HEIGHT;

        return $this;
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge([
            'width' => $this->width,
            'height' => $this->height,
        ], parent::jsonSerialize());
    }
}
