<?php

namespace Wainwright\CasinoDog\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;
use Wainwright\CasinoDog\Jobs\GameslistImporterProcessGame;
use Wainwright\CasinoDog\CasinoDog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GameRespinTemplate extends Eloquent  {
    protected $table = 'wainwright_gamerespin_template';
    protected $timestamp = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'gid',
        'game_data',
        'game_type',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function save_game_template($gid, $game_data, $game_type)
    {
        $data_morp = new CasinoDog();
        $data ??= [];
        $data = $data_morp->morph_array($data);
        $extra_data ??= [];
        $extra_data = $data_morp->morph_array($extra_data);
        $logger = new GameRespinTemplate();
        $logger->gid = $gid;
        $logger->game_data = $game_data;
        $logger->game_type = $game_type;
		$logger->timestamps = true;
        $logger->enabled = true;
		$logger->save();
        Log::debug($game_type.' - Datalogger: '.json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function retrieve_game_template($gid, $game_type)
    {
        try {
        $game = collect(GameRespinTemplate::all()->where('gid', $gid)->random(1));
        $game = json_decode($game, true);
        return $game[0]['game_data'];
        } catch(\Exception $e) {
            return NULL;
        }
    }



}