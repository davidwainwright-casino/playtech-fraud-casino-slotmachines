<?php
namespace Wainwright\CasinoDog\Controllers\Game\iSoftbet;

use Wainwright\CasinoDog\Controllers\Game\SessionsHandler;
use Wainwright\CasinoDog\CasinoDog;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Wainwright\CasinoDog\Controllers\Game\GameKernel;
use Wainwright\CasinoDog\Models\MetaData;
use Wainwright\CasinoDog\Models\Gameslist;

class iSoftbetSessions extends iSoftbetMain
{
    use GameKernelTrait;


    public function extra_game_metadata($gid)
    {
        return false;
    }


    public function fresh_game_session($game_id, $method) {

        if($method === 'demo_method') {
            $demo_identifier = $this->get_game_identifier($game_id);
            $demo_url = 'https://stage-game-launcher-lux.isoftbet.com/1015/200609?identifier='.$demo_identifier.'&lang=EN&cur=USD&background=0&mode=0&';
            $http = Http::get($demo_url);
            $origin_gamelink = $this->in_between('gameLink: "', '"', $http);
            $origin_identifier = $this->in_between('identifier: "', '"', $http);
            $origin_get = Http::retry(2, 3000)->get($origin_gamelink);
            $adaptive = $this->in_between($origin_identifier, '\/', $origin_get);
            $origin_gamelink_identifier = $this->in_between('data-game-link="', '"', $origin_get);


            if($adaptive) {
                $insert_extra = $origin_identifier.$adaptive.'/';
                $explode = explode($origin_identifier.'.html', $origin_gamelink);
                $origin_gamelink = $explode[0].$insert_extra.$origin_identifier.'.html'.$explode[1];
                $http_new = Http::get($origin_gamelink);
                $origin_gamelink_identifier = $this->in_between('data-game-link="', '"', $http_new);
                $origin_gamelink = $explode[0].$insert_extra.$origin_gamelink_identifier.$explode[1];
                Log::debug($origin_gamelink);
                $static_url = $explode[0].$insert_extra;
            }


            $url = $origin_gamelink;
            //$url = 'https://static-fun-stable.isoftbet.com/demo_presentation/stage/html/html5/'.$game_id.'/'.$game_id.'/'.$game_id.'.html?name=1015,fun&password=fun&lang=en&currency=USD&funmode=true&rulesUrl=https%3A%2F%2Fstatic-fun-stable.isoftbet.com%2Fdemo_presentation%2Fstage%2Fhtml%2Fhtml5%2Frules%2Fen%2F'.$game_id.'_rules.html%3Flid%3D1015%26country%3DNL&skinid=200605&channelautodetection=OFF&allowFullScreen=false&enableConsole=true&newSkinIDFormat=true&gameLinkPOSTcontent=&licenseId=1016&operator=0&providerId=1&identifier='.$game_id.'&cur=USD&historyURL=&lobbyURL=&turboMode=false&revision=&country=NL&environment_type=staging&environment_domain=win.radio.fm&server_id=14&userId=&username=&token=&gapLauncherScriptExtension=null&italy_aams_id=null&italy_participation_id=null&italy_rebuy_icon=false&background=1&mode=0';
            Log::debug($url);
            //$url = "https://stage-game-launcher-lux.isoftbet.com/1015/200605?identifier=pulse_plunderin_pirates_hw&lang=EN&cur=USD&background=1&mode=0&";

            //$url = "https://static-fun-stable.isoftbet.com/demo_presentation/stage/html/html5/pulse_plunderin_pirates_hw/pulse_plunderin_pirates_hw_r3/pulse_plunderin_pirates_hw_game.html?name=1015,fun&password=fun&lang=en&currency=USD&funmode=true&rulesUrl=https%3A%2F%2Fstatic-fun-stable.isoftbet.com%2Fdemo_presentation%2Fstage%2Fhtml%2Fhtml5%2Frules%2Fen%2Fpulse_plunderin_pirates_hw_rules.html%3Flid%3D1015%26country%3DNL&skinid=200605&channelautodetection=OFF&allowFullScreen=false&cachebuster=&enableConsole=true&newSkinIDFormat=true&gameLinkPOSTcontent=&licenseId=1015&operator=0&providerId=1&identifier=pulse_plunderin_pirates_hw&cur=USD&historyURL=&lobbyURL=&turboMode=false&revision=&country=NL&environment_type=staging&environment_domain=luxemburg.isoftbet.com&server_id=14&userId=&username=&token=&gapLauncherScriptExtension=null&italy_aams_id=1&italy_participation_id=null&italy_rebuy_icon=false&background=0&mode=0";

            //$url = "https://static-common.isoftbet.com/games/html/html5/pulse_lost_boys_loot/pulse_lost_boys_loot_r40/pulse_lost_boys_loot.html?name=255,fun&password=fun&lang=en&currency=EUR&funmode=true&rulesUrl=https%3A%2F%2Fstatic-common.isoftbet.com%2Fgames%2Fhtml%2Fhtml5%2Frules%2Fen%2Fpulse_lost_boys_loot_rules.html%3Flid%3D255%26country%3DNL&skinid=907953&channelautodetection=ON&allowFullScreen=true&cachebuster=458f45f99fb32a1428657514f4f99545676220e5&enableConsole=false&gameLinkPOSTcontent=&licenseId=255&operator=0&providerId=1&identifier=pulse_lost_boys_loot&cur=EUR&historyURL=&lobbyURL=&turboMode=false&revision=&country=NL&environment_type=production&environment_domain=luxemburg.isoftbet.com&server_id=13&userId=&username=&token=&gapLauncherScriptExtension=null&italy_aams_id=null&italy_participation_id=null&italy_rebuy_icon=false&entry=c0276be44230b2d6d6043ad45953a281-1663524823&player_id=test&mode=0&background=0";
            //$url = 'https://game-launcher-lux.isoftbet.com/255/907953?lang=en&cur=EUR&mode=0&background=1';
            //$url = "https://static-common.isoftbet.com/games/html/html5/pulse_lost_boys_loot/pulse_lost_boys_loot_r40/pulse_lost_boys_loot_game.html?name=255,fun&password=fun&lang=en&currency=EUR&funmode=true&rulesUrl=https%3A%2F%2Fstatic-common.isoftbet.com%2Fgames%2Fhtml%2Fhtml5%2Frules%2Fen%2Fpulse_lost_boys_loot_rules.html%3Flid%3D255%26country%3DNL&skinid=907953&channelautodetection=ON&allowFullScreen=true&cachebuster=3c3e3d49ab22d01983b002114ab4c38744681dff&enableConsole=false&gameLinkPOSTcontent=&licenseId=255&operator=0&providerId=1&identifier=pulse_lost_boys_loot&cur=EUR&historyURL=&lobbyURL=&turboMode=false&revision=&country=NL&environment_type=production&environment_domain=luxemburg.isoftbet.com&server_id=13&userId=&username=&token=&gapLauncherScriptExtension=null&italy_aams_id=null&italy_participation_id=null&italy_rebuy_icon=false&mode=0&background=0";



            $http_get = Http::retry(2, 3000)->get($url);
            $data = [
                'identifier' => $origin_identifier,
                'html' => $http_get,
                'static_url' => $static_url,
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

        $game = $this->fresh_game_session($game_id, 'demo_method');
        $changed_content = $this->modify_game($token_internal, $game);
        $link_query = explode('?', $game['link']);
        $link_query = $link_query[1];
        $response = [
            'content' => $changed_content,
            'original_content' => $game['html'],
            'query' => $link_query,
            'game_url' => $game['link'],
            'session' => $this->get_internal_session($internal_token)['data'],
        ];
        return $response;
    }


}