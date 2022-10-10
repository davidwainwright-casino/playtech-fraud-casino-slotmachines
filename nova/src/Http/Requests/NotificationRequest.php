<?php

namespace Laravel\Nova\Http\Requests;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Laravel\Nova\Http\Resources\NotificationResource;
use Laravel\Nova\Notifications\Notification;

class NotificationRequest extends NovaRequest
{
    /**
     * @return AnonymousResourceCollection
     */
    public function notifications()
    {
        return NotificationResource::collection(
            Notification::whereNotifiableId($this->user()->getKey())
                ->latest()
                ->take(100)
                ->get()
        );
    }

    public function unreadCount(): int
    {
        return Notification::unread()->whereNotifiableId(
            $this->user()->getKey()
        )->count();
    }
}
