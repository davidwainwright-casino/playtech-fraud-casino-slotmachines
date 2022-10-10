<?php

namespace Laravel\Nova\Fields;

/**
 * @method static static make(mixed $name = 'Avatar', string|null $attribute = 'name')
 */
class UiAvatar extends Avatar
{
    /**
     * UI-Avatars settings.
     *
     * @var array
     */
    protected $settings = [
        'size' => 300,
        'color' => '7F9CF5',
        'background' => 'EBF4FF',
    ];

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @return void
     */
    public function __construct($name = 'Avatar', $attribute = 'name')
    {
        parent::__construct($name, $attribute ?? 'name');

        $this->exceptOnForms();

        $this->maxWidth(50);
    }

    /**
     * Resolve the field's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        parent::resolve($resource, $attribute);

        $callback = function () {
            return 'https://ui-avatars.com/api/?'.http_build_query(array_merge($this->settings, ['name' => $this->value]));
        };

        $this->preview($callback)->thumbnail($callback);
    }

    /**
     * Set the font-size.
     *
     * @param  float|int  $fontSize
     * @return $this
     */
    public function fontSize($fontSize)
    {
        $this->settings['font-size'] = $fontSize;

        return $this;
    }

    /**
     * Set the color.
     *
     * @param  string  $color
     * @return $this
     */
    public function color($color)
    {
        $this->settings['color'] = ltrim($color, '#');

        return $this;
    }

    /**
     * Set the background color.
     *
     * @param  string  $color
     * @return $this
     */
    public function backgroundColor($color)
    {
        $this->settings['background'] = ltrim($color, '#');

        return $this;
    }

    /**
     * Set the font weight to bold.
     *
     * @return $this
     */
    public function bold()
    {
        $this->settings['bold'] = 'true';

        return $this;
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge([
            'indexName' => '',
        ], parent::jsonSerialize());
    }
}
