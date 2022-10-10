<?php
namespace Wainwright\CasinoDog\Controllers\Game\PragmaticPlay;

use Wainwright\CasinoDog\Controllers\Game\SessionsHandler;
use Wainwright\CasinoDog\CasinoDog;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Wainwright\CasinoDog\Controllers\Game\GameKernel;
use Wainwright\CasinoDog\Models\MetaData;

class PragmaticPlaySessions extends PragmaticPlayMain
{
    use GameKernelTrait;

    # Disclaimer: this should be made into a job and/or contract on any type of high load
    public function pragmaticplay_gameid_transformer($game_id, $direction)
    {
        if($direction === 'explode') {
            try {
                $games_kernel = new GameKernel;
                $games = $games_kernel->get_gameslist();
                $select_game = $games->where('gid', $game_id)->first();
                if(!$select_game) {
                    Log::warning('Error '.$game_id.' not found in gameslist');
                    return false;
                }
                $demolink = $select_game['demolink'];

                $origin_game_id = CasinoDog::in_between('gameSymbol=', 'u0026', $demolink);
                $origin_game_id = CasinoDog::remove_back_slashes($origin_game_id);
                Log::debug('Game ID transformed to '.$origin_game_id);

                return $demolink;
            } catch (\Exception $exception) {
                Log::warning('Errored trying to transform & explode game_id on pragmaticplay_gameid_transformer() function in PragmaticPlayController.'.$exception);
                return false;
            }
        } elseif($direction === 'concat') {
            $concat = 'softswiss/'.$game_id;
            return $concat;
        }
        Log::warning('Transform direction not supported, use concat or explode on pragmaticplay_gameid_transformer().');
        return false;
    }

    public function extra_game_metadata($gid)
    {
        // Add in extra fields that you need for whatever reason on games
        // Launch the metadata job
        $games_kernel = new GameKernel;
        $games = $games_kernel->get_gameslist();
        $select_game = $games->where('gid', $gid)->first();
        if(!$select_game) {
            return false;
        }
        $demo_link = $select_game->demolink;
        if(!$demo_link) {
            Log::warning('On extra_game_metadata, processed demo link does not seem to be available, which is needed for pragmatic play game_id transformation. Game ID: '.$gid);
            return false;
        }
        $explode = explode('?', $demo_link);
        parse_str($explode[1], $q_arr);

        if(isset($q_arr['gameSymbol'])) {
            if($q_arr['gameSymbol'] !== NULL) {
            $new_game_id = $q_arr['gameSymbol'];
            $data = [
                'key' => $gid,
                'type' => 'extra_data_gameslist',
                'extended_key' => 'gameSymbol',
                'value' => $q_arr['gameSymbol'],
                'object_data' => '[]',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $db_action = MetaData::where('key', $gid)->where('type', 'extra_data_gameslist')->first();
                if($db_action) {
                    $db_action->update(['value' => $q_arr['gameSymbol'], 'updated_at' => now()]);
                } else {
                    MetaData::insert($data);
                }
            }
            $user_agent = '[]';
            $test_session = $this->fresh_game_session($new_game_id, 'demo_method', $user_agent);
            return $data; //success?
        }
        return false;
    }

    public function fresh_game_session($game_id, $method) {

        if($method === 'redirect') {

        $url = "https://demogamesfree.pragmaticplay.net/gs2c/openGame.do?gameSymbol=".$game_id."&websiteUrl=&platform=WEB&jurisdiction=99&lobby_url=&lang=en&cur=USD&isBridge=true&max_rnd_win=100";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $html = curl_exec($ch);
        $redirectURL = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        $launcherTest = Http::withOptions([
            'verify' => false,
        ])->get($redirectURL);

        $parts = parse_url($redirectURL);
        parse_str($parts['query'], $query);
        return array(
            'html_content' => $launcherTest->body(),
            'modified_content' => NULL,
            'query' => $query,
            'token_original' => $query['mgckey'],
        );

        }

        if($method === 'token_only') {
            $url = "https://demogamesfree.pragmaticplay.net/gs2c/openGame.do?gameSymbol=".$game_id."&websiteUrl=&platform=WEB&jurisdiction=99&lobby_url=&lang=en&isBridge=true&cur=USD";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $html = curl_exec($ch);
            $redirectURL = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);

            $launcherTest = Http::withOptions([
                'verify' => false,
            ])->get($redirectURL);

            $token_only_parts = parse_url($redirectURL);
            parse_str($token_only_parts['query'], $token_only_query);
            return $token_only_query['mgckey'];
        }

        if($method === 'demo_method') {
            $url = "https://demogamesfree.pragmaticplay.net/gs2c/openGame.do?gameSymbol=".$game_id."&websiteUrl=&platform=WEB&jurisdiction=99&isBridge=true&lobby_url=&lang=en&cur=USD";
            Log::debug('Game url request: '.$url);
            $http_get = Http::retry(2, 3000)->get($url);
            return $http_get;
        }

        // Add in additional grey methods here, specify the method on the internal session creation when a session is requested, don't split this here
        return 'generateSessionToken() method not supported';
    }

    public function create_session(string $internal_token)
    {
        $select_session = $this->get_internal_session($internal_token);
        if($select_session['status'] !== 200) { //internal session not found
               return false;
        }

        $token_internal = $select_session['data']['token_internal'];
        $game_id = $select_session['data']['game_id_original'];
        $user_agent = $select_session['data']['user_agent'] ?? '[]';
        $check_active_session = $this->find_previous_active_session($internal_token);

        $get_meta_data = $this->extra_game_metadata($game_id);
        $game_symbol = $get_meta_data['value'];

        $game_content = $this->fresh_game_session($game_symbol, 'redirect');
        $origin_session_token = $game_content['token_original'];


        if($origin_session_token === false)
        {
            Log::critical('Not being able to select play_token, even though the status & original game data seems correct. Possibly game source/structure has changed itself - disable game before proceeding to investigate thoroughly. '.json_encode($origin_session_token));
            return false;
        }

        SessionsHandler::sessionUpdate($token_internal, 'token_original', $origin_session_token); //update session table with the real game session token
        $changed_content = $this->modify_game($token_internal, $game_content['html_content']);
        $game_content['modified_content'] = $changed_content;
        $response = [
            'content' => $game_content,
            'internal_token' => $internal_token,
            'session' => $this->get_internal_session($internal_token)['data'],
        ];
        return $response;
    }


}
