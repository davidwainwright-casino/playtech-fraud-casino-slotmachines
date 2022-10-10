<?php

namespace Wainwright\CasinoDog\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class BridgeSessions extends Eloquent  {
    protected $table = 'wainwright_bridge_sessions';
    protected $timestamp = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'bridge_session_token',
        'entry_session_token',
        'parent_session',
        'active',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}