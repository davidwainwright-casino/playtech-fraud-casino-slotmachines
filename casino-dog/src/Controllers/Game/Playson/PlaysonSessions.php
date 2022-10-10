<?php
namespace Wainwright\CasinoDog\Controllers\Game\Playson;
use Illuminate\Support\Facades\Http;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Wainwright\CasinoDog\Models\Gameslist;

class PlaysonSessions extends PlaysonMain
{
    use GameKernelTrait;

    public function extra_game_metadata($gid)
    {
        return false;
    }

    public function fresh_game_session($game_id, $method, $token_internal = NULL)
    {
        if($method === 'demo_method') {


            $demo_link = $this->get_game_demolink($game_id);
            //  $demo_link = str_replace('modelplat.com', 'playsonsite-dgm.ps-gamespace.com', $demo_link);
            $parts = parse_url($demo_link);
            parse_str($parts['query'], $query_parts);
            //$demo_link = 'https://playsonsite-dgm.ps-gamespace.com/launch?partner=playsonsite-prod&gameName='.$query_parts['gamename'].'&lang=en&wl=pl_gate';
            $gameName = str_replace('pls_', '', $query_parts['gamename']);
            $gameName = str_replace('wolf_power_megaways', 'wolf_power_mega', $gameName);
            $gameName = str_replace('_hold_and_win', '', $gameName);
            $gameName = str_replace('diamond_fortunator', 'diamond_fort', $gameName);
            $gameName = str_replace('buffalo_power_megaways', 'buffalo_megaways', $gameName);
            $gameName = str_replace('buffalo_power_christmas', 'buffalo_xmas', $gameName);
            
            $build_url = 'https://playsonsite-dgm.ps-gamespace.com/launch?key=TEST1100000&partner=playsonsite-prod&gameName='.$gameName.'&lang=en&wl=pl_gate';
            
            $html = Http::get($build_url);
            $query_explode = explode('?', $build_url);
            $data = [
                'origin_gameid' => $query_parts['gamename'],
                'html' => $html,
                'query' => $query_explode[1],
                'link' => $demo_link,
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


        $game = $this->fresh_game_session($game_id, 'demo_method', $token_internal);
        $html_content_modify = $this->modify_game($token_internal, $game['html']);

        $response = [
            'html' => $html_content_modify,
            'origin_gameid' => $game['origin_gameid'],
            'query' => $game['query'],
            'token' => $internal_token,
            'link' => $game['link'],
        ];
        return $response;
    }


}
