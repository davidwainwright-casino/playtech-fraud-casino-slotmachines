<?php

namespace Wainwright\CasinoDog\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Route;

class GamesThumbnails extends Eloquent  {
    protected $table = 'wainwright_games_thumbnails';
    protected $timestamp = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'img_gid',
        'img_url',
        'img_ext',
        'ownedBy',
        'active',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function insert_thumbnail($img_gid, $img_url, $img_ext, $gameslist_id)
    {
        $data = [
            'img_gid' => $img_gid,
            'img_url' => $img_url,
            'img_ext' => $img_ext,
            'ownedBy' => $gameslist_id,
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        self::insert($data);
        return true;
    }

    public function gameslist()
    {
        return $this->belongsTo('Wainwright\CasinoDog\Models\Gameslist', 'ownedBy');
    }
}