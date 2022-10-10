<?php
namespace Wainwright\CasinoDog\Controllers\Game\Bgaming;

use Wainwright\CasinoDog\Controllers\Game\SessionsHandler;
use Wainwright\CasinoDog\CasinoDog;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Wainwright\CasinoDog\Controllers\Game\Bgaming\BgamingMain;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BgamingSessions extends BgamingMain
{
    use GameKernelTrait;

    public function fresh_game_session($game_id, $method, $user_agent) {

        if($method === 'demo_method') {
            $game_id = self::bgaming_gameid_transformer($game_id, 'explode');
            $url = 'https://bgaming-network-mga.com/play/'.$game_id.'/FUN?server=demo';
            $http_get = Http::withHeaders($user_agent)->retry(2, 4000)->get($url);
            return $http_get;
        }
        // Add in additional grey methods here, specify the method on the internal session creation when a session is requested, don't split this here
        return 'generateSessionToken() method not supported';
    }

    public function try_continue_previous_session($game_id, $token_original, $user_agent)
    {
        $game_id = self::bgaming_gameid_transformer($game_id, 'explode');
        $url = 'https://bgaming-network-mga.com/games/'.$game_id.'/FUN?play_token='.$token_original;
        $http_get = Http::withHeaders($user_agent)->retry(2, 4000)->get($url);
        return $http_get;
    }

    public static function bgaming_gameid_transformer($game_id, $direction)
    {
        if($direction === 'explode') {
            try {
                $explode_game = explode('/', $game_id);
                $exploded_game_id = $explode_game[1];
                return $exploded_game_id;
            } catch (\Exception $exception) {
                Log::warning('Errored trying to transform & explode game_id on bgaming_gameid_transformer() function in bgamingcontroller.');
                return false;
            }
        } elseif($direction === 'concat') {
            $concat = 'softswiss/'.$game_id;
            return $concat;
        }
        Log::warning('Transform direction not supported, use concat or explode on bgaming_gameid_transformer().');
        return false;
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

        if($select_session['data']['extra_meta']['launcher_behaviour'] === 'selenium_retrieval') {
            //return self::selenium_retrieval($game_id);
        }

        if($check_active_session['status'] === 404)
        { // create a new session because no previous one is found
            $retrieve_play_session = $this->fresh_game_session($game_id, 'demo_method', $user_agent);
            if($retrieve_play_session->status() !== 200) {
                return false;
            }
        }
         else // else try to connect to the previous active session
        {
            $old_token_to_transfer = $check_active_session['token_original'];
            $retrieve_play_session = $this->try_continue_previous_session($game_id, $old_token_to_transfer, $user_agent);
            if($retrieve_play_session->status() !== 200)
            { // create new session if trying to continue previous session fails
                $retrieve_play_session = $this->fresh_game_session($game_id, 'demo_method', $user_agent);
                if($retrieve_play_session->status() !== 200)
                {
                    $this->expire_internal_session($internal_token);
                    return false;
                }
            }
            else
            { // change internal session to expired
               $this->expire_internal_session($internal_token);
            }
        }

        $game_content = $retrieve_play_session;
        $origin_session_token = CasinoDog::in_between('\"play_token\":\"', '\",\"', $game_content);

        if($origin_session_token === false)
        {
            Log::critical('Not being able to select play_token, even though the status & original game data seems correct. Possibly game source/structure has changed itself - disable game before proceeding to investigate thoroughly. '.json_encode($origin_session_token));
            return false;
        }

        SessionsHandler::sessionUpdate($token_internal, 'token_original', $origin_session_token); //update session table with the real game session token
        $changed_content = $this->modify_game($token_internal, $game_content->body());

        $cookie = $game_content->cookies()->getCookieByName('_games_session')->getValue();
        $array = [
            'cookie' => $cookie,
            'html' => $changed_content
        ];
        return $array;
    }


}