<?php
namespace Wainwright\CasinoDog\Controllers\Game\Mascot;
use Illuminate\Support\Facades\Http;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Wainwright\CasinoDog\Models\Gameslist;

class MascotSessions extends MascotMain
{
    use GameKernelTrait;

    public function extra_game_metadata($gid)
    {
        return false;
    }

    public function fresh_game_session($game_id, $method, $token_internal = NULL)
    {
        if($method === 'redirect') {
            $game_identifier = explode('/', $game_id);
            $url = 'https://demo.mascot.games/run/'.$game_identifier[1];
            //$url = 'https://exapi.mascot.games/eva/2020-05-22?cid=parimatch&productId='.$game_identifier[1].'&lang=en&targetChannel=desktop&consumerId=mascot';
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

            $html = Http::withOptions([
                'verify' => false,
            ])->get($redirectURL);

            $session_id = str_replace('https://', '', $redirectURL);
            $session_id = explode('.', $session_id)[0];

    
            $data = [
                'html' => $html,
                'link' => $redirectURL,
                'origin_session' => $session_id,
                'origin_game_id' => $game_identifier,
            ];
            return $data;
        }

        if($method === 'continued_session') {
            $game_identifier = explode('/', $game_id);
            $select_session = $this->get_internal_session($token_internal)['data'];
            $link = 'https://'.$select_session['token_original'].'.mascot.games';
            $html = Http::get($link);

            if($html->status() !== 200) { //failed to continue old session, creating new
                $game = $this->fresh_game_session($game_id, 'redirect', $token_internal);
                $this->update_session($token_internal, 'token_original', $game['origin_session']); //update session table with the real game session
                return $game;
            }

            $data = [
                'html' => $html,
                'link' => $link,
                'origin_session' => $select_session['token_original'],
                'origin_game_id' => $game_identifier,
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

    public function create_session(string $internal_token)
    {
        $select_session = $this->get_internal_session($internal_token);
        if($select_session['status'] !== 200) { //internal session not found
               return false;
        }

        $token_internal = $select_session['data']['token_internal'];
        $game_id = $select_session['data']['game_id_original'];

        if($select_session['data']['token_original'] === 0) {
            $game = $this->fresh_game_session($game_id, 'redirect', $token_internal);
            $this->update_session($internal_token, 'token_original', $game['origin_session']); //update session table with the real game session
        } else {
            $game = $this->fresh_game_session($game_id, 'continued_session', $token_internal);
        }

        $html_content_modify = $this->modify_game($token_internal, $game['html']);


        $response = [
            'html' => $html_content_modify,
            'origin_session' => $game['origin_session'],
            'origin_game_id' => $game['origin_game_id'],
            'token' => $internal_token,
            'link' => $game['link'],
        ];
        return $response;
    }


}
