<?php
namespace Wainwright\CasinoDog\Models;
use \Illuminate\Database\Eloquent\Model as Eloquent;
use Wainwright\CasinoDog\CasinoDog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DataLogger extends Eloquent  {
    protected $table = 'wainwright_datalogger';
    protected $timestamp = true;
    protected $primaryKey = 'id';
    protected $fillable = [
        'type',
        'uuid',
    ];
    protected $casts = [
        'data' => 'json',
        'extra_data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public static function log_count() {
        $value = Cache::remember('datalogger:log_count', 300, function () {
            return Datalogger::count();
        });
        return $value;
    }
    public static function auto_clean()
    {
        Datalogger::truncate();
        Cache::pull('datalogger:log_count');
        Log::notice('Truncated datalogger collection automatic because surpassed 5000 entries.');
    }

    public static function save_log($type, $data, $extra_data = NULL)
    {
        if(self::log_count() > 5000) {
            self::auto_clean();
            Log::notice('Truncated datalogger collection automatic because surpassed 5000 entries.');
        }

        $data_morp = new CasinoDog();

        $data ??= [];
        $data = $data_morp->morph_array($data);
        $extra_data ??= [];
        $extra_data = $data_morp->morph_array($extra_data);
        $logger = new DataLogger();
        $logger->type = $type;
		$logger->uuid = Str::orderedUuid();
		$logger->data = $data;
        $logger->extra_data = $extra_data;
		$logger->timestamps = true;
		$logger->save();
        Log::debug($type.' - Datalogger: '.json_encode($data, JSON_PRETTY_PRINT));
    }




}