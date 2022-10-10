<?php

namespace Wainwright\CasinoDog\Controllers\Game\Bgaming\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class BgamingBonusGames extends Eloquent  {
    protected $table = 'wainwright_bgaming_bonusgames';
    protected $timestamp = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'bonusgame_token',
        'player_id',
        'command',
        'game_id',
    ];
    protected $casts = [
        'replayed' => 'boolean',
        'active' => 'boolean',
        'init_event' => 'json',
        'game_event' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}