<?php

namespace Laravel\Nova;

use Illuminate\Http\Resources\MergeValue;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Laravel\Nova\Contracts\RelatableField;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\HasHelpText;

/**
 * @method static static make(string $name, \Closure|array|iterable $fields = [])
 */
class Panel extends MergeValue implements JsonSerializable
{
    use Macroable, Metable, Makeable, HasHelpText;

    /**
     * The name of the panel.
     *
     * @var string
     */
    public $name;

    /**
     * The panel fields.
     *
     * @var array
     */
    public $data;

    /**
     * The panel's component.
     *
     * @var string
     */
    public $component = 'panel';

    /**
     * Indicates whether the detail toolbar should be visible on this panel.
     *
     * @var bool
     */
    public $showToolbar = false;

    /**
     * The initial field display limit.
     *
     * @var int|null
     */
    public $limit = null;

    /**
     * The help text for the element.
     *
     * @var string
     */
    public $helpText;

    /**
     * Indicates if the panel is collapsable.
     *
     * @var bool
     */
    public $collapsable = false;

    /**
     * Create a new panel instance.
     *
     * @param  string  $name
     * @param  (\Closure():array|iterable)|array  $fields
     * @return void
     */
    public function __construct($name, $fields = [])
    {
        $this->name = $name;

        parent::__construct($this->prepareFields($fields));
    }

    /**
     * Mutate new panel from list of fields.
     *
     * @param  string  $name
     * @param  \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>  $fields
     * @return static
     */
    public static function mutate($name, $fields)
    {
        $first = $fields->first();

        if ($first instanceof ResourceToolElement) {
            return static::make($name)
                ->withComponent($first->component)
                ->withMeta(['fields' => $fields, 'prefixComponent' => false]);
        }

        return tap($first->assignedPanel, function ($panel) use ($name, $fields) {
            $panel->name = $name;
            $panel->withMeta(['fields' => $fields]);
        });
    }

    /**
     * Prepare the given fields.
     *
     * @param  (\Closure():array|iterable)|array|iterable  $fields
     * @return array
     */
    protected function prepareFields($fields)
    {
        return collect(is_callable($fields) ? $fields() : $fields)->each(function ($field) {
            $field->assignedPanel = $this;
            $field->panel = $this->name;
        })->all();
    }

    /**
     * Get the default panel name for the given resource.
     *
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function defaultNameForDetail(Resource $resource)
    {
        return __(':resource Details: :title', [
            'resource' => $resource->singularLabel(),
            'title' => (string) $resource->title(),
        ]);
    }

    /**
     * Get the default panel name for a create panel.
     *
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function defaultNameForCreate(Resource $resource)
    {
        return __('Create :resource', [
            'resource' => (string) $resource->singularLabel(),
        ]);
    }

    /**
     * Get the default panel name for the update panel.
     *
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function defaultNameForUpdate(Resource $resource)
    {
        return __('Update :resource: :title', [
            'resource' => $resource->singularLabel(),
            'title' => $resource->title(),
        ]);
    }

    /**
     * Get the default panel name for the given resource.
     *
     * @param  \Laravel\Nova\Resource  $resource
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return string
     */
    public static function defaultNameForViaRelationship(Resource $resource, NovaRequest $request)
    {
        $field = $request->newViaResource()
            ->availableFields($request)
            ->filter(function ($field) use ($request) {
                return $field instanceof RelatableField
                    && $field->resourceName === $request->resource
                    && $field->relationshipName() === $request->viaRelationship;
            })->first();

        return $field->name;
    }

    /**
     * Display the toolbar when showing this panel.
     *
     * @return $this
     */
    public function withToolbar()
    {
        $this->showToolbar = true;

        return $this;
    }

    /**
     * Set the number of initially visible fields.
     *
     * @param  int  $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Set the Vue component key for the panel.
     *
     * @param  string  $component
     * @return $this
     */
    public function withComponent($component)
    {
        $this->component = $component;

        return $this;
    }

    /**
     * Get the Vue component key for the panel.
     *
     * @return string
     */
    public function component()
    {
        return $this->component;
    }

    /**
     * Set the panel as collapsable.
     *
     * @return $this
     */
    public function collapsable()
    {
        $this->collapsable = true;

        return $this;
    }

    /**
     * Set the panel as collapsable.
     *
     * @return $this
     */
    public function collapsible()
    {
        return $this->collapsable();
    }

    /**
     * Set the width for the help text tooltip.
     *
     * @param  string  $helpWidth
     * @return $this
     *
     * @throws \Exception
     */
    public function helpWidth($helpWidth)
    {
        throw new \Exception('Help width is not supported on panels.');
    }

    /**
     * Return the width of the help text tooltip.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getHelpWidth()
    {
        throw new \Exception('Help width is not supported on panels.');
    }

    /**
     * Prepare the panel for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge([
            'collapsable' => $this->collapsable,
            'component' => $this->component(),
            'name' => $this->name,
            'showToolbar' => $this->showToolbar,
            'limit' => $this->limit,
            'helpText' => $this->getHelpText(),
        ], $this->meta());
    }
}
