<?php

namespace Laravel\Nova\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Nova\Contracts\ImpersonatesUsers;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

/**
 * @property string $email
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (app()->bound(NovaRequest::class)) {
            $resourceClass = Nova::resourceForModel($this->resource);

            if (! is_null($resourceClass)) {
                $resource = new $resourceClass($this->resource);
                $avatar = $resource->resolveAvatarField(app(NovaRequest::class));

                if (! is_null($avatar)) {
                    $avatar = $avatar->resolveThumbnailUrl();
                }
            }
        }

        return array_merge(
            parent::toArray($request),
            [
                'avatar' => $avatar ?? null,
                'canImpersonate' => method_exists($this->resource, 'canImpersonate') && $this->resource->canImpersonate() === true,
                'impersonating' => app(ImpersonatesUsers::class)->impersonating($request),
            ],
        );
    }
}
