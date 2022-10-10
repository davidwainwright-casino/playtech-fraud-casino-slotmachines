<?php

namespace Laravel\Nova\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $email
 */
class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->getKey(),
            'user_id' => $this->resource->notifiable_id,
            'component' => data_get($this->resource->data, 'component'),
            'message' => data_get($this->resource->data, 'message'),
            'actionText' => data_get($this->resource->data, 'actionText'),
            'actionUrl' => data_get($this->resource->data, 'actionUrl'),
            'icon' => data_get($this->resource->data, 'icon'),
            'type' => data_get($this->resource->data, 'type'),
            'iconClass' => data_get($this->resource->data, 'iconClass'),
            'created_at_friendly' => $this->resource->created_at->diffForHumans(),
            'created_at' => $this->resource->created_at->toIso8601String(),
            'read_at' => $this->resource->read_at,
        ];
    }
}
