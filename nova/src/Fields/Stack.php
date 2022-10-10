<?php

namespace Laravel\Nova\Fields;

use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @method static static make(string $name, string|array|null $attribute = null, array $lines = [])
 */
class Stack extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'stack-field';

    /**
     * Indicates if the element should be shown on the creation view.
     *
     * @var (callable(\Laravel\Nova\Http\Requests\NovaRequest):bool)|bool
     */
    public $showOnCreation = false;

    /**
     * Indicates if the element should be shown on the update view.
     *
     * @var (callable(\Laravel\Nova\Http\Requests\NovaRequest, mixed):bool)|bool
     */
    public $showOnUpdate = false;

    /**
     * The contents of the Stack field.
     *
     * @var array|\Illuminate\Support\Collection
     */
    public $lines;

    /**
     * Create a new Stack field.
     *
     * @param  string  $name
     * @param  string|array<int, class-string<\Laravel\Nova\Fields\Field>|callable>|null  $attribute
     * @param  array<int, class-string<\Laravel\Nova\Fields\Field>|callable>  $lines
     * @return void
     */
    public function __construct($name, $attribute = null, $lines = [])
    {
        if (is_array($attribute)) {
            $lines = $attribute;
            $attribute = null;
        }

        parent::__construct($name, $attribute);

        $this->lines = $lines;
    }

    /**
     * Resolve the field's value for display.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolveForDisplay($resource, $attribute = null)
    {
        $this->prepareLines($resource, $attribute);
    }

    /**
     * Prepare the stack for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'lines' => $this->lines->all(),
        ]);
    }

    /**
     * Prepare each line for serialization.
     *
     * @param  mixed  $resource
     * @param  string  $attribute
     * @return void
     */
    public function prepareLines($resource, $attribute = null)
    {
        $this->ensureLinesAreResolveable();

        $request = app(NovaRequest::class);

        $this->lines = $this->lines->filter(function ($field) use ($request, $resource) {
            if ($request->isResourceIndexRequest()) {
                return $field->isShownOnIndex($request, $resource);
            }

            return $field->isShownOnDetail($request, $resource);
        })->values()->each->resolveForDisplay($resource, $attribute);
    }

    /**
     * Ensure that each line for the field is resolvable.
     *
     * @return void
     */
    protected function ensureLinesAreResolveable()
    {
        $this->lines = collect($this->lines)->map(function ($line) {
            if (is_callable($line)) {
                return Line::make('Anonymous', $line);
            }

            return $line;
        });
    }
}
