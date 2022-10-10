<?php
namespace Wainwright\CasinoDog\Controllers\Game\Hacksaw;

use Wainwright\CasinoDog\Controllers\Game\SessionsHandler;
use Wainwright\CasinoDog\CasinoDog;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Wainwright\CasinoDog\Controllers\Game\GameKernel;
use Wainwright\CasinoDog\Models\Gameslist;

class HacksawSessions extends HacksawMain
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
            $url = str_replace('https://rgs-demo.hacksawgaming.com/api', env('APP_URL').'/api/games/hacksaw/'.$token_internal.'/'.$game_id, $demo_link);
            $url = str_replace('token=demo', 'token='.$token_internal, $url);
            $url = str_replace('mode=2', 'mode=0', $url);

            $data = [
                'link' => $url,
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

        $response = [
            'link' => $game['link'],
        ];
        return $response;
    }


}
