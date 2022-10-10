<?php

namespace Laravel\Nova\Http\Resources;

use Laravel\Nova\Http\Requests\ResourceCreateOrAttachRequest;

class ReplicateViewResource extends CreateViewResource
{
    /**
     * From Resource ID.
     *
     * @var string|int|null
     */
    protected $fromResourceId;

    /**
     * Construct a new Create View Resource.
     *
     * @param  string|int|null  $fromResourceId
     * @return void
     */
    public function __construct($fromResourceId = null)
    {
        $this->fromResourceId = $fromResourceId;
    }

    /**
     * Get current resource for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceCreateOrAttachRequest  $request
     * @return \Laravel\Nova\Resource
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function newResourceWith(ResourceCreateOrAttachRequest $request)
    {
        return tap($request->newResourceWith(
            tap($request->findModelQuery($this->fromResourceId), function ($query) use ($request) {
                $resource = $request->resource();
                $resource::replicateQuery($request, $query);
            })->firstOrFail()
        ))->authorizeToReplicate($request)
        ->replicate();
    }
}
