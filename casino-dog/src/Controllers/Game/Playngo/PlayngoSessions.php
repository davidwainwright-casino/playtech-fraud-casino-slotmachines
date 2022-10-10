<?php
namespace Wainwright\CasinoDog\Controllers\Game\Playngo;

use Wainwright\CasinoDog\Controllers\Game\SessionsHandler;
use Wainwright\CasinoDog\CasinoDog;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Wainwright\CasinoDog\Controllers\Game\GameKernel;
use Wainwright\CasinoDog\Models\Gameslist;

class PlayngoSessions extends PlayngoMain
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
            $game_id = $this->in_between("gid=", "\&", $demo_link);
            $url = 'https://wainwrighted.herokuapp.com/https://asccw.playngonetwork.com/Casino/IframedView?pid=2&gid='.$game_id.'&lang=en_US&practice=1&channel=desktop&div=flashobject&width=100%25&height=100%25&user=&password=&ctx=&demo=2&brand=&lobby=&rccurrentsessiontime=0&rcintervaltime=0&rcaccounthistoryurl=&rccontinueurl=&rcexiturl=&rchistoryurlmode=&autoplaylimits=0&autoplayreset=0&callback=flashCallback&rcmga=&resourcelevel=0&hasjackpots=False&country=&pauseplay=&playlimit=&selftest=&sessiontime=&coreweburl=https://asccw.playngonetwork.com/&showpoweredby=True';
            //$url = 'https://fmtcw.playngonetwork.com/Casino/IframedView?pid=594&gid='.$game_id.'&lang=en_US&practice=1&channel=desktop&div=flashobject&width=100%25&height=100%25&user=&password=&ctx=&demo=0&brand=&lobby=&rccurrentsessiontime=0&currency=USD&rcintervaltime=0&rcaccounthistoryurl=&rccontinueurl=&rcexiturl=&rchistoryurlmode=&autoplaylimits=0&autoplayreset=0&callback=flashCallback&rcmga=&resourcelevel=0&hasjackpots=False&country=&pauseplay=&playlimit=&selftest=&sessiontime=&coreweburl=https://fmtcw.playngonetwork.com/&showpoweredby=true';


            $http = Http::get($url);

            $data = [
                'link' => $url,
                'html' => $http,
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
        $changed_content = $this->modify_game($token_internal, $game);

        $response = [
            'link' => $game['link'],
            'html' => $changed_content,
            'token' => $internal_token,
        ];
        return $response;
    }


}
