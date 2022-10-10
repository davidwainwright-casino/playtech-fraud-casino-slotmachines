<?php

namespace Laravel\Nova\Lenses;

use ArrayAccess;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Illuminate\Http\Resources\DelegatesToResource;
use Illuminate\Support\Str;
use JsonSerializable;
use Laravel\Nova\AuthorizedToSee;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Makeable;
use Laravel\Nova\Nova;
use Laravel\Nova\ProxiesCanSeeToGate;
use Laravel\Nova\ResolvesActions;
use Laravel\Nova\ResolvesCards;
use Laravel\Nova\ResolvesFilters;
use Laravel\Nova\SupportsPolling;
use stdClass;

abstract class Lens implements ArrayAccess, JsonSerializable, UrlRoutable
{
    use AuthorizedToSee,
        ConditionallyLoadsAttributes,
        DelegatesToResource,
        Makeable,
        ProxiesCanSeeToGate,
        ResolvesActions,
        ResolvesCards,
        ResolvesFilters,
        SupportsPolling;

    /**
     * The displayable name of the lens.
     *
     * @var string
     */
    public $name;

    /**
     * The underlying model resource instance.
     *
     * @var \Illuminate\Database\Eloquent\Model|\stdClass
     */
    public $resource;

    /**
     * Execute the query for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    abstract public static function query(LensRequest $request, $query);

    /**
     * Get the fields displayed by the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    abstract public function fields(NovaRequest $request);

    /**
     * Create a new lens instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $resource
     * @return void
     */
    public function __construct($resource = null)
    {
        $this->resource = $resource ?: new stdClass;
    }

    /**
     * Set the resource of the lens.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get the displayable name of the lens.
     *
     * @return string
     */
    public function name()
    {
        return $this->name ?: Nova::humanize($this);
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return Str::slug($this->name(), '-', null);
    }

    /**
     * Get the actions available on the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array<int, \Laravel\Nova\Actions\Action>
     */
    public function actions(NovaRequest $request)
    {
        return $request->newResourceWith(
            $this->resource instanceof Model ? $this->resource : $request->model()
        )->actions($request);
    }

    /**
     * Resolve the given fields to their values.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function resolveFields(NovaRequest $request)
    {
        return $this->availableFields($request)
            ->filterForIndex($request, $this->resource)
            ->withoutListableFields()
            ->resolve($this->resource)
            ->authorized($request)
            ->resolveForDisplay($this->resource);
    }

    /**
     * Resolve the filterable fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field&\Laravel\Nova\Contracts\FilterableField>
     */
    public function filterableFields(NovaRequest $request)
    {
        return $this->availableFields($request)
                    ->withOnlyFilterableFields()
                    ->authorized($request);
    }

    /**
     * Get the fields that are available for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection
     */
    public function availableFields(NovaRequest $request)
    {
        return new FieldCollection(array_values($this->filter($this->fields($request))));
    }

    /**
     * Prepare the lens for JSON serialization using the given fields.
     *
     * @param  \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>  $fields
     * @return array
     */
    protected function serializeWithId(FieldCollection $fields)
    {
        return [
            'id' => $fields->whereInstanceOf(ID::class)->first() ?: ID::forModel($this->resource),
            'fields' => $fields->all(),
        ];
    }

    /**
     * Prepare the lens for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name(),
            'uriKey' => $this->uriKey(),
        ];
    }
}
