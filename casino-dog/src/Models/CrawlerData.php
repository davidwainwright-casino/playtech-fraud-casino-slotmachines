<?php

namespace Wainwright\CasinoDog\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Route;
use Wainwright\CasinoDog\Jobs\UrlscanInit;

class CrawlerData extends Eloquent  {
    protected $table = 'wainwright_crawlerdata';
    protected $timestamp = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'url',
        'state',
        'state_message',
        'extra_id',
        'type',
        'result',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expired_bool' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function update_data($extra_id, $key, $newValue)
    {
        $retrieve_session_from_database = CrawlerData::where('extra_id', $extra_id)->first();
        if(!$retrieve_session_from_database) {
            return false;
        }
        try {
            $new = $retrieve_session_from_database->update([
                $key => $newValue
            ]);
        } catch (\Exception $exception) {
            return false;
        }
        $data = $retrieve_session_from_database;
        $data[$key] = $newValue;
        return $data;
    }


    public function crawl_request(string $url, string $type)
    {
        if($type === 'urlscan') {
            UrlscanInit::dispatch($url);
        }
    }

}