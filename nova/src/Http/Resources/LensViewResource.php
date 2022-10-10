<?php

namespace Laravel\Nova\Http\Resources;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Query\ApplySoftDeleteConstraint;

class LensViewResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @return array
     */
    public function toArray($request)
    {
        $lens = $this->authorizedLensForRequest($request);

        $query = $request->newQuery();

        if ($request->resourceSoftDeletes()) {
            (new ApplySoftDeleteConstraint)->__invoke($query, $request->trashed);
        }

        $paginator = $lens->query($request, $query);

        if ($paginator instanceof Builder) {
            $paginator = $paginator->simplePaginate($request->perPage());
        }

        return [
            'name' => $request->lens()->name(),
            'resources' => $request->toResources($paginator->getCollection()),
            'prev_page_url' => $paginator->previousPageUrl(),
            'next_page_url' => $paginator->nextPageUrl(),
            'per_page' => $paginator->perPage(),
            'per_page_options' => $request->resource()::perPageOptions(),
            'softDeletes' => $request->resourceSoftDeletes(),
            'hasId' => $lens->availableFields($request)->whereInstanceOf(ID::class)->isNotEmpty(),
            'polling' => $lens::$polling,
            'pollingInterval' => $lens::$pollingInterval * 1000,
            'showPollingToggle' => $lens::$showPollingToggle,
        ];
    }

    /**
     * Get authorized resource for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @return \Laravel\Nova\Lenses\Lens
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizedLensForRequest(LensRequest $request)
    {
        return $request->lens();
    }
}
