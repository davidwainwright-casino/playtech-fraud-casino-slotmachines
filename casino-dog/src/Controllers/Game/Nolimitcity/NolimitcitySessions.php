<?php
namespace Wainwright\CasinoDog\Controllers\Game\Nolimitcity;
use Illuminate\Support\Facades\Http;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Wainwright\CasinoDog\Models\Gameslist;

class NolimitcitySessions extends NolimitcityMain
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
            $html = Http::get($demo_link);
            $query_explode = explode('?', $demo_link);
            $data = [
                'html' => $html,
                'query' => $query_explode[1],
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
            'query' => $game['query'].'&operator=nolimitcity',
            'token' => $internal_token,

        ];
        return $response;
    }


}
