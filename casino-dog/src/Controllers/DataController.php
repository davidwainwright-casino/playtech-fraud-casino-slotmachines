<?php

namespace Wainwright\CasinoDog\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Wainwright\CasinoDog\Models\Gameslist;
use Wainwright\CasinoDog\CasinoDog;
use Wainwright\CasinoDog\Controllers\ProxyController;
use Wainwright\CasinoDog\Models\RawGameslist;
use Wainwright\CasinoDog\Models\GameImporterJob;

class DataController
{

    public static function get_demolink_proxy($url)
    {
        $host = self::get_host($url);
        $allowedhosts = $host.',www.'.$host;
        $proxy = new ProxyController();
        return $proxy->launch_job('game_session_static', $url, $allowedhosts);
    }

    public static function get_gameslist_proxy($url)
    {
        $proxy = new ProxyController();
        return $proxy->launch_job('json_softswiss', $url);
    }

    public static function get_demolink_direct($url)
    {
        $http_client = Http::timeout(10)->get($url);
        return $http_client;
    }

    public static function route_get_demolink($url, $schema)
    {
        try {
        if(config('casino-dog.wainwright_proxy.get_demolink') === 1) {
            return self::get_demolink_proxy($url);
        } else {
            return self::get_demolink_direct($url);
        }
        } catch(\Exception $e) {
            $casino_dog = new CasinoDog();
            $casino_dog->save_log('DataController()', 'Failed to get_demolink('.$url.'): '.$e->getMessage());
            return false;
        }
    }

    /**
     * Get clean demo link from source
     *
     */
    public static function get_demolink($gid)
    {
        $select = Gameslist::where('gid', $gid)->first();
        $source_schema = strtolower($select['source_schema']);

        if($source_schema === 'parimatch') {
            $url = 'https://pari-match.com/en/casino/slots/game/'.$select['gid'];
        } elseif($source_schema === 'softswiss') {
        $url = 'https://www.'.$select['source'].$select['origin_demolink'];
        } else {
            return false; // source not supported
        }

        if($select['provider'] === 'netent') {
            $select->update([
                'demolink' => $url
            ]);
            return true;
        }


        $get = self::route_get_demolink($url, $select['source_schema']);

        if($source_schema === 'parimatch') {
            $kernel = new DataController;
            $retrieve = $kernel->parimatch_curl('https://pari-match.com/service-discovery/service/pm-casino/api/eva/slots/lobby/game/demo/'.$gid.'?lobbyUrl=https%3A%2F%2Fpari-match.com%2Fen%2Fcasino%2Fslots');
            $final_url = $retrieve['response']['url'];
            if($select['provider'] === 'hacksaw') {
                $explode_url = explode('?', $final_url);
                $parsed_query = $kernel->parse_query($explode_url[1]);
                $http_get_version = json_decode((Http::get('https://static-live.hacksawgaming.com/'.$parsed_query['productId'].'/version.json')), true);
                $final_url = 'https://static-live.hacksawgaming.com/'.$parsed_query['productId'].'/'.$http_get_version['version'].'/index.html?language=en&channel=desktop&gameid='.$parsed_query['productId'].'&mode=2&token=demo&lobbyurl=&env=https://rgs-demo.hacksawgaming.com/api&alwaysredirect=true';
            }
        }

        if($source_schema === 'softswiss') {

        if($select['provider'] === 'playngo') {
            $origin_demo_launch = self::in_between('{\"desktop_url\":\"', '\",\"return_url\"', $get);
            $back_slash_removal = self::remove_back_slashes(urldecode($origin_demo_launch)); //remove backslashes
            $final_url = str_replace("u0026", "&", $back_slash_removal);
        } else {
            if (!strpos($get, 'game_url')) {
                $url = 'https://' . $select['source'] . $select['origin_demolink'];
                $get = self::route_get_demolink($url, $select['source_schema']);
                if (!strpos($get, 'game_url')) {
                    $casino_dog = new CasinoDog();
                    $casino_dog->save_log('DataController()', 'Failed to get_demolink: ' . $url, $get);
                    return dd($get);
                }
                }
                $origin_demo_launch = self::in_between('{\"game_url\":\"', '\",\"strategy\"', $get);
                $back_slash_removal = self::remove_back_slashes(urldecode($origin_demo_launch)); //remove backslashes
                $final_url = str_replace("u0026", "&", $back_slash_removal);
        }
        }

        if($final_url === NULL) {
            Log::warning('Failed to process get_demolink() for '.$url);
            return false;
        }
        $select->update([
            'demolink' => $final_url
        ]);

        $extra_meta = config('gameconfig.'.$select->provider.'.extra_game_metadata');
        if($extra_meta) {
            if($extra_meta !== 0) {
                //BuildExtraMetaGameslist::dispatch($gid);
            }
        }
    }

    public function gameslist_batch($batch_id)
    {
        $importer_job = new GameImporterJob();
        $select_job = $importer_job->where('id', $batch_id)->first();
        $select_job->update([
            'state' => 'JOB_STARTED',
        ]);

        if($select_job->schema === 'parimatch') {
            $select_job->update([
                'link' => 'https://pari-match.com/service-discovery/service/lobby/api/eva/slots/lobby/games?_limit=9500&_start=0&status=ACTIVE',
            ]);
            $http_get = $this->parimatch_curl($select_job->link);


        } elseif($select_job->schema === 'softswiss') {
            if(config('casino-dog.wainwright_proxy.get_gamelist') === 1) {
            $http_get = $this->get_gameslist_proxy($select_job->link);
            } else {
            $http_get = $this->softswiss_curl($select_job->link);
            }
        }


        if($http_get['response'] === NULL || $http_get === NULL) {
            $select_job->update([
                'state' => 'JOB_FAILED',
                'state_message' => 'Did not pass health check on start of job because of empty response on '.$select_job->link,
            ]);
        } else {
            $gamelist_cached_id = 'game_importer_result::'.Str::random(40);
            $gamelist_cached = Cache::put($gamelist_cached_id, $http_get['response']);
            $select_job->update([
                'state' => 'JOB_HEALTH_PASSED',
                'state_message' => 'Starting to process game now, response is cached under ID: '.$gamelist_cached_id,
            ]);

            if($select_job->schema === 'parimatch') {
                foreach ($http_get['response']['items'] as $data) {
                    $game_options = [
                        'gameID' => $data['id'],
                        'batch_id' => $select_job->id,
                        'schema' => $select_job->schema,
                    ];
                    $job = new GameImporterJob;
                    $job->process_game($game_options, $data);
                }

            } else {

            foreach ($http_get['response'] as $gameID => $data) {
                $game_options = [
                    'gameID' => $gameID,
                    'batch_id' => $select_job->id,
                    'schema' => $select_job->schema,
                ];
                $job = new GameImporterJob;
                $job->process_game($game_options, $data);
            }

            }
            $select_job->update([
                'state' => 'JOB_BATCH_LAUNCHED',
            ]);
        }
    }

    public function gameslist_process_parimatch($game_options, $data) {
        $select_job = GameImporterJob::where('id', $game_options['batch_id'])->first();
        $gameID = $game_options['gameID'];
        $url = urldecode($select_job->link);
        $batch_id = $select_job->id;
        $parse = parse_url($url);
        $originTarget = preg_replace('/^www\./', '', $parse['host']);
        $filterkey = $select_job->filter_key;
        $filtervalue = $select_job->filter_value;
        $source_schema = $select_job->source_schema;
        $typeGame = 'slots';
        $stringified_game = json_encode($data);

        if(isset($data['category']['live'])) {
            $typeGame = 'live';
        }
        $hasJackpot = 0;
        if(str_contains($stringified_game, 'jackpot')) {
            $hasJackpot = 1;
        }

        $hasBonusBuy = 0;
        if(str_contains($stringified_game, 'buy-feature')) {
            $hasBonusBuy = 1;
        }
        $demoMode = 0;
        if($data['demoModeAvailable'] === true) {
            $demoMode = 1;
        }
        $demoPrefix = 0;
        $typeRatingGame = 0;
        $internal_origin_realmoneylink = [];
        $rawobject = [];


        $prepareArray = array(
            'gid' => $gameID,
            'batch' => $batch_id,
            'slug' => $data['id'],
            'name' => $data['translationKey'],
            'provider' => $data['provider'],
            'type' => $typeGame,
            'typeRating' => $typeRatingGame,
            'popularity' => rand(60, 150),
            'bonusbuy' => $hasBonusBuy,
            'jackpot' => $hasJackpot,
            'demoplay' => $demoMode,
            'origin_demolink' => $demoPrefix,
            'source' => $originTarget,
            'source_schema' => 'parimatch',
            'realmoney' => json_encode($internal_origin_realmoneylink),
            'rawobject' => json_encode($rawobject),
            'mark_transfer' => 0,
        );

        if($filterkey !== NULL && $filtervalue !== NULL) {
            if($prepareArray[$filterkey] === $filtervalue) {
                RawGameslist::insert($prepareArray);
                $count = ($select_job->imported_games ?? 0) + 1;
                $select_job->update([
                    'imported_games' => $count
                ]);
            }
        } else {
            RawGameslist::insert($prepareArray);
            $count = ($select_job->imported_games ?? 0) + 1;
            $select_job->update([
                'imported_games' => $count
            ]);

        }

    }

    public function gameslist_process_softswiss($game_options, $data) {
        $gameID = $game_options['gameID'];
        $select_job = GameImporterJob::where('id', $game_options['batch_id'])->first();
        $url = urldecode($select_job->link);
        $batch_id = $select_job->id;
        $parse = parse_url($url);
        $originTarget = preg_replace('/^www\./', '', $parse['host']);
        $filterkey = $select_job->filter_key;
        $filtervalue = $select_job->filter_value;
        $source_schema = $select_job->source_schema;
        $explodeSSid = explode('/', $gameID);
        $bindTogether = $explodeSSid[0].':'.$explodeSSid[1];
        $typeGame = 'casino';
        $demoMode = 0;
        $demoPrefix = 0;
        $typeRatingGame = 0;
        $internal_origin_realmoneylink = [];

        if(isset($data['demo'])) {
            $demoMode = true;
            $demoPrefix = urldecode($data['demo']);
            if($originTarget === 'bitstarz.com') {
                $demoPrefix = str_replace('http://bitstarz.com', '', $demoPrefix);
            }
        }

        if(isset($data['real'])) {
            $internal_origin_realmoneylink = $data['real'];
        }

        $stringifyDetails = json_encode($data['collections']);
        if(str_contains($stringifyDetails, 'slots')) {
            $typeGame = 'slots';
            if(isset($data['collections']['slots'])) {
                $typeRatingGame = $data['collections']['slots'];
            } else {
                $typeRatingGame = 100;
            }
        }

        if(str_contains($stringifyDetails, 'live')) {
            $typeGame = 'live';
            if(isset($data['collections']['live'])) {
                $typeRatingGame = $data['collections']['live'];
            } else {
                $typeRatingGame = 100;
            }
        }

        if(str_contains($stringifyDetails, 'buy')) {
            $hasBonusBuy = 1;
        } else {
            $hasBonusBuy = 0;
        }

        if(str_contains($stringifyDetails, 'jackpot')) {
            $hasJackpot = 1;
        } else {
            $hasJackpot = 0;
        }

        $prepareArray = array(
            'gid' => $gameID,
            'batch' => $batch_id,
            'slug' => $bindTogether,
            'name' => $data['title'],
            'provider' => $data['provider'],
            'type' => $typeGame,
            'typeRating' => $typeRatingGame,
            'popularity' => $data['collections']['popularity'],
            'bonusbuy' => $hasBonusBuy,
            'jackpot' => $hasJackpot,
            'demoplay' => $demoMode,
            'origin_demolink' => $demoPrefix,
            'source' => $originTarget,
            'source_schema' => 'softswiss',
            'realmoney' => json_encode($internal_origin_realmoneylink),
            'rawobject' => json_encode($data),
            'mark_transfer' => 0,
        );

        if($filterkey !== NULL && $filtervalue !== NULL) {
            if($prepareArray[$filterkey] === $filtervalue) {
                RawGameslist::insert($prepareArray);
                $count = ($select_job->imported_games ?? 0) + 1;
                $select_job->update([
                    'imported_games' => $count
                ]);
            }
        } else {
            RawGameslist::insert($prepareArray);
            $count = ($select_job->imported_games ?? 0) + 1;
            $select_job->update([
                'imported_games' => $count
            ]);

        }
    }

    public static function getGames()
    {
        $cache_length = 120;
        if($cache_length === 0) {
            return Gameslist::all()->sortBy('popularity');
        }
        $value = Cache::remember('getGames', 120, function () {
            return Gameslist::all()->sortBy('popularity');
        });
        return $value;
    }

    // Helper intended to parse "in_between" values mainly for html content (resource intensive)
    public static function in_between($a, $b, $data)
    {
        preg_match('/'.$a.'(.*?)'.$b.'/s', $data, $match);
        if(!isset($match[1])) {
            return false;
        }
        return $match[1];
    }

    // Helper to get host from url
    public static function get_host($url)
    {
        $url = urldecode($url);
        $parse = parse_url($url);
        $host = preg_replace('/^www\./', '', $parse['host']);
        return $host;
    }

    public static function remove_back_slashes($string)
    {
        $string=implode("",explode("\\",$string));
        return stripslashes(trim($string));
    }



    public function parimatch_curl($url) {
        //$url = "https://pari-match.com/service-discovery/service/lobby/api/eva/slots/lobby/games?_limit=100&_start=0&status=ACTIVE";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
           "accept: */*",
           "accept-language: en-ZA,en;q=0.9",
           "cache-control: no-cache",
           "content-type: application/json",
           "cookie: BETBOOK_LANGUAGE=en; _sp_ses.6a34=*; _sp_id.6a34=bb6c1b7d-d226-44c9-993f-0ff51ebe5a9f.1663697112.1.1663697160..b65bdc98-919e-4251-9dfe-4a546ed660a9..e1663ea1-2a15-4983-b82e-a4202472d55e.1663697111994.7",
           "pragma: no-cache",
           "referer: https://pari-match.com/en/casino/lobby",
           "sec-ch-ua-mobile: ?0",
           "sec-fetch-dest: empty",
           "sec-fetch-mode: cors",
           "sec-fetch-site: same-origin",
           "sentry-trace: 4c0c2408ab1c42b1b36d81d123197c4c-8456178347df850f-1",
           "user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36",
           "x-brand: COM",
           "x-channel: DESKTOP_AIR_PM",
           "x-lang: en",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        curl_close($curl);

        $torespond = [
            'url' => $url,
            'response' => json_decode($response, true),
            'response_status_code' => $statusCode,
        ];

        return $torespond;

    }

    public function softswiss_curl($url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
           "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.14.3; rv:105.0) Gecko/20100101 Firefox/105.0",
           "Accept: application/vnd.softswiss.v1+json",
           "Accept-Language: en-ZA",
           "Referer: https://www.bets.io/",
           "Content-Type: undefined",
           "Origin: https://www.bets.io",
           "DNT: 1",
           "Connection: keep-alive",
           "Cookie: _ga_GWC3ZK8F5X=GS1.1.1660891793.1.1.1660892558.54.0.0; _ga=GA1.1.101142630.1660891794; locale=ImVuIg%3D%3D--faa52eee2a616938ef2a4bf113bd5f0e77a9168a; dateamlutsk-_zldp=M6KbIcofZ5O%2Fb8iHioM3OZl5fNPRZr9mmT88Em2Lnxm%2FnKMCXUt%2Bzgl8UHx%2Bhpy4lLnzf3o1QSQ%3D; dateamlutsk-_zldt=fb5b5329-bab5-4210-9fed-e1a8dc0a2eff-0",
           "Sec-Fetch-Dest: empty",
           "Sec-Fetch-Mode: cors",
           "Sec-Fetch-Site: same-site",
           "Pragma: no-cache",
           "Cache-Control: no-cache",
           "TE: trailers",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15); //timeout in seconds
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);

        curl_close($curl);


        $torespond = [
            'url' => $url,
            'response' => json_decode($response, true),
            'response_status_code' => $statusCode,
        ];

        return $torespond;
    }

    public function build_response_query($query)
    {
        $resp = http_build_query($query);
        $resp = urldecode($resp);
        return $resp;
    }

    public function parse_query($query_string)
    {
        parse_str($query_string, $q_arr);
        return $q_arr;
    }

}
