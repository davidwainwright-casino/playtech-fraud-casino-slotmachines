<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NotificationRequest;
use Laravel\Nova\Notifications\Notification;

class NotificationReadController extends Controller
{
    /**
     * Mark the given notification as read.
     *
     * @param  NotificationRequest  $request
     * @param  Notification  $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(NotificationRequest $request, Notification $notification)
    {
        $notification->update(['read_at' => now()]);

        return response()->json();
    }
}
