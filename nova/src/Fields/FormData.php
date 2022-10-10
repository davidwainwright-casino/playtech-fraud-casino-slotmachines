<?php

namespace Laravel\Nova\Fields;

use Illuminate\Support\Fluent;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @extends \Illuminate\Support\Fluent<TKey, TValue>
 */
class FormData extends Fluent
{
    /**
     * The Request instance.
     *
     * @var \Laravel\Nova\Http\Requests\NovaRequest
     */
    protected $request;

    /**
     * Create a new fluent instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return void
     */
    public function __construct($attributes, NovaRequest $request)
    {
        parent::__construct($attributes);

        $this->request = $request;
    }

    /**
     * Make fluent payload from request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  array<string, mixed>  $fields
     * @return static
     */
    public static function make(NovaRequest $request, array $fields)
    {
        if (! is_null($request->resource) && ! is_null($request->resourceId)) {
            $fields["resource:{$request->resource}"] = $request->resourceId;
        }

        if (! is_null($request->viaResource) && ! is_null($request->viaResourceId)) {
            $fields["resource:{$request->viaResource}"] = $request->viaResourceId;
        }

        if (! is_null($request->relatedResource) && ! is_null($request->relatedResourceId)) {
            $fields["resource:{$request->relatedResource}"] = $request->relatedResourceId;
        }

        return new static($fields, $request);
    }

    /**
     * Make fluent payload from request only on specific keys.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  array<int, string>  $onlyAttributes
     * @return static
     */
    public static function onlyFrom(NovaRequest $request, array $onlyAttributes)
    {
        return static::make($request, $request->only($onlyAttributes));
    }

    /**
     * Get an resource attribute from the fluent instance.
     *
     * @param  string  $uriKey
     * @param  mixed  $default
     * @return mixed
     */
    public function resource($uriKey, $default = null)
    {
        $key = "resource:{$uriKey}";

        if (! empty($this->request->viaRelationship)
            && ($uriKey === $this->request->relatedResource || $uriKey === $this->request->viaResource)
        ) {
            return $this->get($key, $this->get($this->request->viaRelationship, $default));
        }

        return $this->get($key, $default);
    }
}
