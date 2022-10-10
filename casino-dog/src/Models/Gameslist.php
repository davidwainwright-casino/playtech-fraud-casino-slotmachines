<?php

namespace Wainwright\CasinoDog\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;
use Wainwright\CasinoDog\Models\MetaData;
use DB;

class Gameslist extends Eloquent  {

    protected $table = 'wainwright_gameslist';
    protected $timestamp = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'gid',
        'gid_extra',
        'batch',
        'slug',
        'name',
        'provider',
        'type',
        'typeRating',
        'popularity',
        'bonusbuy',
        'jackpot',
        'demoplay',
        'demolink',
        'origin_demolink',
        'source',
        'source_schema',
        'realmoney',
        'method',
        'image',
        'enabled',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'realmoney' => 'json',
        'rawobject' => 'json',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public static function build_list() {
        $query = DB::table('wainwright_gameslist')->get();
        if($query->count() === 1) {
            $game = $query;
            $games_array = array(
            'gid' => $game->gid,
            'batch' => $game->batch,
            'slug' => $game->slug,
            'name' => $game->name,
            'provider' => $game->provider,
            'method' => $game->method,
            'source' => $game->source,
            'popularity' => $game->popularity,
            'demoplay' => $game->demoplay,
            'demolink' => $game->demolink,
            'origin_demolink' => $game->origin_demolink,
            'enabled' => $game->enabled,
            'meta' => MetaData::retrieve_extended_game($game->gid)
            );
        } elseif($query->count() > 1)
        foreach($query as $game) {
            $games_array[] = array(
                'gid' => $game->gid,
                'slug' => $game->slug,
                'batch' => $game->batch,
                'name' => $game->name,
                'provider' => $game->provider,
                'method' => $game->method,
                'source' => $game->source,
                'popularity' => $game->popularity,
                'demolink' => $game->demolink,
                'demoplay' => $game->demoplay,
                'origin_demolink' => $game->origin_demolink,
                'enabled' => $game->enabled,
                'meta' => MetaData::retrieve_extended_game($game->gid)
            );
        } else {
            $message = array('status' => 'error', 'data' => NULL, 'message' => 'No games found at all');
            return response()->json($message, 404);
        }

        $message = array('status' => 'success', 'data' => $games_array);
        return response()->json($message, 200);
    }


    public static function providers(){
        $query = Gameslist::distinct()->get('provider');

        foreach($query as $provider) {
            $provider_array[] = array(
                'id' => $provider->provider,
                'slug' => $provider->provider,
                'provider' => $provider->provider,
                'name' => ucfirst($provider->provider),
                'methods' => 'demoModding',
            );
        }
        return $provider_array;
    }

    public function gamesthumbnails(){
        return $this->belongsToMany('Wainwright\CasinoDog\Models\GamesThumbnails', 'ownedBy');
    }


}

