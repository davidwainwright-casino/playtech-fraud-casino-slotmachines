<?php

namespace Laravel\Nova\Notifications;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $table = 'nova_notifications';

    protected $guarded = [];

    public $incrementing = false;

    public $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Return the notifiable relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Scope the given query by unread notifications.
     *
     * @return void
     */
    public static function scopeUnread(Builder $query)
    {
        $query->whereNull('read_at');
    }
}
