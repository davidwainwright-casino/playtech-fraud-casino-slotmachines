<?php

namespace Wainwright\CasinoDog\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class MetaData extends Eloquent  {
    protected $table = 'wainwright_metadata';
    protected $timestamp = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'key',
        'type',
        'value',
        'extended_key',
    ];
    protected $casts = [
        'active' => 'boolean',
        'object_data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function retrieve_extended_game($gid)
    {
        $find = MetaData::where('key', $gid)->first();
        if($find) {
            $count = MetaData::where('key', $gid)->count();
            if($count > 1) { // build array for multiple query results
                $find = MetaData::where('key', $gid)->get();
                foreach($find as $game) {
                    $data[] = array(
                        $game['type'] => array(
                        'value' => $game['value'],
                        'extended_key' => $game['extended_key'],
                        'object_data' => $game['object_data'],
                        'active' => $game['active']
                        ),
                    );
                };
            } else { // build array for a single query found
            $game = $find;
            $data = array(
                $game['type'] => array(
                'value' => $game['value'],
                'extended_key' => $game['extended_key'],
                'object_data' => $game['object_data'],
                'active' => $game['active']
                ),
            );
            }
        } else { // no extended data found on given gid
            $data = NULL;
        }
        return $data;
    }

}