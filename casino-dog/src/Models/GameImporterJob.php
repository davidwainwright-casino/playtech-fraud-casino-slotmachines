<?php

namespace Wainwright\CasinoDog\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Route;
use Wainwright\CasinoDog\Jobs\GameslistImporterBatch;
use Wainwright\CasinoDog\Jobs\GameslistImporterProcessGame;

class GameImporterJob extends Eloquent  {
    protected $table = 'wainwright_job_gameimporter';
    protected $timestamp = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'link',
        'filter_key',
        'filter_value',
        'schema',
        'state',
        'state_message',
        'proxy',
        'imported_games',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function process_game($game_options, $data)
    {
        GameslistImporterProcessGame::dispatch($game_options, $data);
    }

    public function start_job($id)
    {
        GameslistImporterBatch::dispatch($id);
    }
}