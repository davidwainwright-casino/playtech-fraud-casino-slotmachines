<?php

namespace Laravel\Nova\Fields;

use Exception;
use Laravel\Nova\Badge as BadgeComponent;

class Badge extends Field
{
    /**
     * The text alignment for the field's text in tables.
     *
     * @var string
     */
    public $textAlign = 'center';

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'badge-field';

    /**
     * The labels that should be applied to the field's possible values.
     *
     * @var array<array-key, string>
     */
    public $labels;

    /**
     * The callback used to determine the field's label.
     *
     * @var (callable(mixed):string)|null
     */
    public $labelCallback;

    /**
     * The mapping used for matching custom values to in-built badge types.
     *
     * @var array<array-key, string>
     */
    public $map;

    /**
     * Indicates if the field should show icons.
     *
     * @var bool
     */
    public $withIcons = false;

    /**
     * The built-in badge types and their corresponding CSS classes.
     *
     * @var array<array-key, string>
     */
    public $types = [];

    /**
     * The icons that should be applied to the field's possible values.
     *
     * @var array<array-key, string>
     */
    public $icons = [
        'success' => 'check-circle',
        'info' => 'information-circle',
        'danger' => 'exclamation-circle',
        'warning' => 'exclamation-circle',
    ];

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|\Closure|callable|object|null  $attribute
     * @param  (callable(mixed, mixed, ?string):mixed)|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->addTypes(BadgeComponent::$types);

        $this->exceptOnForms();
    }

    /**
     * Add badge types and their corresponding CSS classes to the built-in ones.
     *
     * @param  array<array-key, string>  $types
     * @return $this
     */
    public function addTypes(array $types)
    {
        $this->types = array_merge($this->types, $types);

        return $this;
    }

    /**
     * Set the badge types and their corresponding CSS classes.
     *
     * @param  array<array-key, string>  $types
     * @return $this
     */
    public function types(array $types)
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Set the labels for each possible field value.
     *
     * @param  array<array-key, string>  $labels
     * @return $this
     */
    public function labels(array $labels)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * Set the callback to be used to determine the field's displayable label.
     *
     * @param  callable(mixed):string  $labelCallback
     * @return $this
     */
    public function label(callable $labelCallback)
    {
        $this->labelCallback = $labelCallback;

        return $this;
    }

    /**
     * Map the possible field values to the built-in badge types.
     *
     * @param  array<array-key, string>  $map
     * @return $this
     */
    public function map(array $map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * Set the field to display icons, optionally passing an icon mapping.
     *
     * @return $this
     */
    public function withIcons()
    {
        $this->withIcons = true;

        return $this;
    }

    /**
     * Set the icons for each possible field value.
     *
     * @param  array<array-key, string>  $icons
     * @return $this
     */
    public function icons($icons)
    {
        $this->withIcons = true;
        $this->icons = $icons;

        return $this;
    }

    /**
     * Resolve the Badge's CSS classes based on the field's value.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function resolveBadgeClasses()
    {
        $mappedValue = $this->map[$this->value] ?? $this->value;

        if (! isset($this->types[$mappedValue])) {
            throw new Exception("Error trying to find type [{$mappedValue}] inside of the field's type mapping.");
        }

        return $this->types[$mappedValue];
    }

    /**
     * Resolve the display label for the Badge.
     *
     * @return string
     */
    public function resolveLabel()
    {
        if (isset($this->labelCallback)) {
            return call_user_func($this->labelCallback, $this->value);
        }

        return $this->labels[$this->value] ?? $this->value;
    }

    /**
     * Resolve the display icon for the Badge.
     *
     * @return string
     */
    public function resolveIcon()
    {
        return $this->icons[$this->value];
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'label' => $this->resolveLabel(),
            'typeClass' => $this->resolveBadgeClasses(),
            'icon' => $this->withIcons ? $this->resolveIcon() : null,
        ]);
    }
}
