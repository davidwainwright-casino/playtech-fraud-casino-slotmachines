<?php

namespace Wainwright\CasinoDog\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;
use Wainwright\CasinoDog\Jobs\TransferToGamelist;
use Wainwright\CasinoDog\Models\GamesThumbnails;

class RawGameslist extends Eloquent  {

    protected $table = 'wainwright_gameslist_raw';
    protected $timestamp = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'gid',
        'slug',
        'batch_id',
        'name',
        'provider',
        'type',
        'typeRating',
        'popularity',
        'bonusbuy',
        'jackpot',
        'demoplay',
        'origin_demolink',
        'demolink',
        'source',
        'source_schema',
        'realmoney',
        'mark_transfer'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'realmoney' => 'json',
        'rawobject' => 'json'
    ];

    public static function transfer_to_gameslist($gid)
    {
        $game = RawGameslist::where('gid', $gid)->first();
        $source_schema = strtolower($game->source_schema);

        $prepareArray = [
            'gid' => $game->gid,
            'batch' => $game->batch,
            'slug' => $game->slug,
            'name' => $game->name,
            'provider' => $game->provider,
            'type' => $game->type,
            'typeRating' => $game->typeRating,
            'popularity' => $game->popularity,
            'bonusbuy' => $game->bonusbuy,
            'jackpot' => $game->jackpot,
            'image' => NULL,
            'demoplay' => $game->demoplay,
            'origin_demolink' => $game->origin_demolink,
            'source' => $game->source,
            'source_schema' => $source_schema,
            'realmoney' => json_encode($game->realmoney),
            'method' => 'demo_method',
            'created_at' => now(),
            'updated_at' => now(),
            'enabled' => 1,
        ];
        $insert = Gameslist::insert($prepareArray);
        $reselect_game = Gameslist::where('gid', $game->gid)->where('batch', $game->batch)->first();
        if($source_schema === 'softswiss') {
        $thumbnail_kernel = new GamesThumbnails();
        $thumbnail_kernel->insert_thumbnail($game->gid, 'https://cdn.softswiss.net/i/s1/'.$game->gid.'.png', 's1', $reselect_game->id);
        $thumbnail_kernel->insert_thumbnail($game->gid, 'https://cdn.softswiss.net/i/s2/'.$game->gid.'.png', 's2', $reselect_game->id);
        $thumbnail_kernel->insert_thumbnail($game->gid, 'https://cdn.softswiss.net/i/s3/'.$game->gid.'.png', 's3', $reselect_game->id);
        } elseif($source_schema === 'parimatch') {
            $url = 'https://parimatch.co.tz/service-discovery/service/pm-casino/img/tr:n-slots_game_image_desktop/Casino/eva/games/'.$gid.'.png';
            $thumbnail_kernel = new GamesThumbnails();
            $thumbnail_kernel->insert_thumbnail($game->gid, $url, 'pari-match', $reselect_game->id);
        }
        $game->delete();
        return $insert;
    }

    public function transfer_gameslist_dispatch($record) {
        $dispatch = TransferToGamelist::dispatch($record->gid);
        if($dispatch) {
            return true;
        }
    }

    public static function provider(){
        $query = self::all()->distinct()->get('provider');

        $providers_array[] = array();
        foreach($query as $provider) {
            $provider_array[] = array(
                'slug' => $provider->provider,
                'provider' => $provider->provider,
                'name' => ucfirst($provider->provider),
            );
        }

        return json_encode($provider_array, true);
    }


}

