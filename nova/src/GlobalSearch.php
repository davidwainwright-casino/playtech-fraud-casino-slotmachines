<?php

namespace Laravel\Nova;

use Laravel\Nova\Contracts\QueryBuilder;
use Laravel\Nova\Http\Requests\NovaRequest;

class GlobalSearch
{
    /**
     * The request instance.
     *
     * @var \Laravel\Nova\Http\Requests\NovaRequest
     */
    public $request;

    /**
     * The resource class names that should be searched.
     *
     * @var array
     */
    public $resources;

    /**
     * Create a new global search instance.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  array  $resources
     * @return void
     */
    public function __construct(NovaRequest $request, $resources)
    {
        $this->request = $request;
        $this->resources = $resources;
    }

    /**
     * Get the matching resources.
     *
     * @return array<int, array<string, mixed>>
     */
    public function get()
    {
        return iterator_to_array($this->getSearchResults(), false);
    }

    /**
     * Get the search results for the resources.
     *
     * @return \Generator
     */
    protected function getSearchResults()
    {
        foreach ($this->resources as $resourceClass) {
            $query = app()->make(QueryBuilder::class, [$resourceClass])->search(
                $this->request, $resourceClass::newModel()->newQuery()->with($resourceClass::$with),
                $this->request->search
            );

            yield from $query->limit($resourceClass::$globalSearchResults)
                ->cursor()
                ->mapInto($resourceClass)
                ->map(function ($resource) use ($resourceClass) {
                    return $this->transformResult($resourceClass, $resource);
                });
        }
    }

    /**
     * Transform the result from resource.
     *
     * @template TResource of \Laravel\Nova\Resource
     *
     * @param  class-string<TResource>  $resourceClass
     * @param  TResource  $resource
     * @return array<string, mixed>
     */
    protected function transformResult($resourceClass, Resource $resource)
    {
        $model = $resource->model();

        return [
            'resourceName' => $resourceClass::uriKey(),
            'resourceTitle' => $resourceClass::label(),
            'title' => (string) $resource->title(),
            'subTitle' => transform($resource->subtitle(), function ($subtitle) {
                return (string) $subtitle;
            }),
            'resourceId' => Util::safeInt($model->getKey()),
            'url' => url(Nova::url('/resources/'.$resourceClass::uriKey().'/'.$model->getKey())),
            'avatar' => $resource->resolveAvatarUrl($this->request),
            'rounded' => $resource->resolveIfAvatarShouldBeRounded($this->request),
            'linksTo' => $resource->globalSearchLink($this->request),
        ];
    }
}
