<?php

namespace Laravel\Nova\Fields;

use Illuminate\Support\Arr;
use Laravel\Nova\Contracts\FilterableField;
use Laravel\Nova\Contracts\Previewable;
use Laravel\Nova\Fields\Filters\TextFilter;
use Laravel\Nova\Fields\Markdown\CommonMarkPreset;
use Laravel\Nova\Fields\Markdown\DefaultPreset;
use Laravel\Nova\Fields\Markdown\ZeroPreset;
use Laravel\Nova\Http\Requests\NovaRequest;

class Markdown extends Field implements FilterableField, Previewable
{
    use Expandable,
        FieldFilterable,
        SupportsDependentFields;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'markdown-field';

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

    /**
     * Indicates the preset the field should use.
     *
     * @var string
     */
    public $preset = 'default';

    /**
     * The built-in presets for the Markdown field.
     *
     * @var string[]
     */
    public $presets = [
        'default' => DefaultPreset::class,
        'commonmark' => CommonMarkPreset::class,
        'zero' => ZeroPreset::class,
    ];

    /**
     * Define the preset the field should use. Can be "commonmark", "zero", and "default".
     *
     * @param  string  $preset
     * @param  string|null  $presetClass
     * @return $this
     */
    public function preset($preset, $presetClass = null)
    {
        if (! is_null($presetClass)) {
            $this->presets[$preset] = $presetClass;
        }

        $this->preset = $preset;

        return $this;
    }

    /**
     * Make the field filter.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\Filters\Filter
     */
    protected function makeFilter(NovaRequest $request)
    {
        return new TextFilter($this);
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function serializeForFilter()
    {
        return transform($this->jsonSerialize(), function ($field) {
            return Arr::only($field, [
                'uniqueKey',
                'name',
                'attribute',
            ]);
        });
    }

    /**
     * Return a preview for the given field value.
     *
     * @param  string  $value
     * @return string
     */
    public function previewFor($value)
    {
        return $this->renderer()->convert($value);
    }

    /**
     * @return \Laravel\Nova\Fields\Markdown\MarkdownPreset
     */
    public function renderer()
    {
        return new $this->presets[$this->preset];
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'shouldShow' => $this->shouldBeExpanded(),
            'preset' => $this->preset,
            'previewFor' => $this->previewFor($this->value ?? ''),
        ]);
    }
}
