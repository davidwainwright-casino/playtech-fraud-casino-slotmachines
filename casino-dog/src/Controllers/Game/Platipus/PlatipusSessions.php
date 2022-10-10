<?php
namespace Wainwright\CasinoDog\Controllers\Game\Platipus;

use Wainwright\CasinoDog\Controllers\Game\SessionsHandler;
use Wainwright\CasinoDog\CasinoDog;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Wainwright\CasinoDog\Controllers\Game\GameKernel;
use Wainwright\CasinoDog\Models\Gameslist;

class PlatipusSessions extends PlatipusMain
{
    use GameKernelTrait;

    public function extra_game_metadata($gid)
    {
        return false;
    }

    public function fresh_game_session($game_id, $method) {

        if($method === 'demo_method') {
            $demo_link = $this->get_game_demolink($game_id);
            $parse_query = $this->parse_query($demo_link);
            $url = "https://betconstruct.platipusgaming.com/onlinecasino/GetGames/GetGameDemo?demo=true&gameconfig=".$parse_query['game']."&lang=&lobby=&lang=&lobby=";
            $http_get = Http::get($url);
            $redirect_url = $this->in_between('https:', 'lobbyURL', $http_get);
            $redirect_url_trim = str_replace('+', '', $redirect_url);
            $redirect_url_trim = str_replace(' ', '', $redirect_url_trim);
            $redirect_url_trim = str_replace('"', '', $redirect_url_trim);
            $final_launch_url = 'https:'.$redirect_url_trim;

            $http = Http::get($final_launch_url);
            $token_only_parts = parse_url($final_launch_url);
            parse_str($token_only_parts['query'], $token_only_query);
            $key = $token_only_query['key'];

            $explode_url = explode('index.html', $final_launch_url);

            $data = [
                'token_original' => $key,
                'html' => $http,
                'static_path' => $explode_url[0],
                'link' => $final_launch_url,
            ];
            return $data;
        }

        // Add in additional grey methods here, specify the method on the internal session creation when a session is requested, don't split this here
        return 'generateSessionToken() method not supported';
    }

    public function get_game_demolink($gid) {
        $select = Gameslist::where('gid', $gid)->first();
        return $select->demolink;
    }


    public function get_game_identifier($gid) {
        $select = Gameslist::where('gid', $gid)->first();
        return $select->gid_extra;
    }

    public function create_session(string $internal_token)
    {
        $select_session = $this->get_internal_session($internal_token);
        if($select_session['status'] !== 200) { //internal session not found
               return false;
        }

        $token_internal = $select_session['data']['token_internal'];
        $game_id = $select_session['data']['game_id_original'];

        $game = $this->fresh_game_session($game_id, 'demo_method');

        $changed_content = $this->modify_game($token_internal, $game);
        SessionsHandler::sessionUpdate($token_internal, 'token_original', $game['token_original']); //update session table with the real game session
        $link_query = explode('?', $game['link']);
        $link_query = $link_query[1];

        $response = [
            'content' => $changed_content,
            'original_content' => $game['html'],
            'original_query' => $link_query,
            'game_url' => $game['link'],
            'session' => $this->get_internal_session($internal_token)['data'],
        ];
        return $response;
    }


}