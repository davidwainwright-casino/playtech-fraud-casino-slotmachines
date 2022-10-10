<?php

namespace Laravel\Nova\Fields;

use Illuminate\Support\Arr;

class Line extends Text
{
    const HEADING = 'extra-large';

    const BASE = 'large';

    const SUBTITLE = 'medium';

    const SMALL = 'small';

    /**
     * The type for the line field.
     *
     * @var string
     */
    public $type = self::BASE;

    /**
     * Extra CSS classes to apply to the line.
     *
     * @var mixed
     */
    public $extraClasses = '';

    /**
     * The line's component.
     *
     * @var string
     */
    public $component = 'line-field';

    /**
     * CSS class lookup table for lines.
     *
     * @var array<string, string>
     */
    public static $classes = [
        self::HEADING => 'text-base font-semibold',
        self::BASE => 'text-base',
        self::SUBTITLE => 'text-xs tracking-loose font-bold uppercase text-80',
        self::SMALL => 'text-xs',
    ];

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|callable|null  $attribute
     * @param  (callable(mixed, mixed, ?string):mixed)|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->exceptOnForms();
    }

    /**
     * Display the line as a heading.
     *
     * @return $this
     */
    public function asHeading()
    {
        $this->type = static::HEADING;

        return $this;
    }

    /**
     * Display the line as a subtitle.
     *
     * @return $this
     */
    public function asSubTitle()
    {
        $this->type = static::SUBTITLE;

        return $this;
    }

    /**
     * Display the line with small styles.
     *
     * @return $this
     */
    public function asSmall()
    {
        $this->type = static::SMALL;

        return $this;
    }

    /**
     * Display the line with base styles.
     *
     * @return $this
     */
    public function asBase()
    {
        $this->type = static::BASE;

        return $this;
    }

    /**
     * Set the extra CSS classes to be applied to the line field.
     *
     * @param  mixed  $classes
     * @return $this
     */
    public function extraClasses($classes)
    {
        $this->extraClasses = $classes;

        return $this;
    }

    /**
     * Get the display classes for the line.
     *
     * @return array<string, string>
     */
    public function getClasses()
    {
        return array_merge(
            Arr::wrap(self::$classes[$this->type]),
            array_filter(Arr::wrap($this->extraClasses))
        );
    }

    /**
     * Prepare the line for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'classes' => $this->getClasses(),
        ]);
    }
}
