<?php
namespace Wainwright\CasinoDog\Controllers\Game\Playngo;

use Wainwright\CasinoDog\Controllers\Game\GameKernel;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PlayngoMain extends GameKernel
{
    use GameKernelTrait;

    /*
    * load_game_session() is where we create/continue any game session and where we initiate the game content
    *
    * @param [type] $data
    * @return void
    */
    public function load_game_session($data) {
        $token = $data['token_internal'];
        $session = new PlayngoSessions();
        $game_content = $session->create_session($token);
        return $this->game_launch($game_content);
    }

    /*
    * game_launch() is where we send the finalized HTML content to the launcher blade view template
    *
    * @param [type] $game_content
    * @return void
    */
    public function game_launch($game_content) {
        return view('wainwright::launcher-content-playngo')->with('game_content', $game_content);
    }


        /*
    * game_launch() is where we send the finalized HTML content to the launcher blade view template
    *
    * @param [type] $game_content
    * @return void
    */
    public function redirect_catch_content(Request $request) {
        $token = $request->token;
        $select_session = $this->get_internal_session($token)['data'];

        $this->update_session($token, 'token_original', $request->sessId); //update session table with the real game session
        $url = $_SERVER['REQUEST_URI'];
        $url = explode('verify_url=', $url);
        $final = str_replace('netentff-game.casinomodule.com', 'win.radio.fm%2Fapi%2Fgames%2Fnetent%2F'.$token, $url[1]);
        return redirect($final);
    }

    /*
    * game_event() is where direct API requests from inside games are received
    *
    * @param Request $request
    * @return void
    */
    public function game_event(Request $request) {
        $event = new PlayngoGame();
        return $event->game_event($request);
    }

    /*
    * error_handle() for handling errors, meant to make similar error pages as original game but can be used for any error handling you need
    *
    * @param [type] $type
    * @param [type] $message
    * @return void
    */
    public function error_handle($type, $message = NULL) {
        if($type === 'incorrect_game_event_request') {
            $message = ['status' => 400, 'error' => $type];
            return response()->json($message, 400);
        }
        abort(400, $message);
    }

    /*
    * dynamic_asset() used to load altered javascript from internal storage
    *
    * @param string $asset_name
    * @param Request $request
    * @return void
    */
    public function demolink_retrieval_method($gid, $data) {



    }


    /*
    * dynamic_asset() used to load altered javascript from internal storage
    *
    * @param string $asset_name
    * @param Request $request
    * @return void
    */
    public function dynamic_asset(string $asset_name, Request $request) {


        if (str_contains($asset_name, 'GameLoader')) {
            $url_query = explode('?', $request->fullUrl());
            if(isset($url_query[1])) {

                $token = explode(';', $asset_name);
                $token = $token[1];
                $select_session = $this->get_internal_session($token)['data'];
                
                $new_url = $_SERVER['REQUEST_URI'];
                $new_url_explode = explode('?', $new_url);
                $new_url = str_replace(';'.$token, '', $new_url_explode[1]);
                $http = Http::get('https://wainwrighted.herokuapp.com/https://asccw.playngonetwork.com/Casino/../Casino/GameLoader?'.$new_url);
                //$http = Http::get('https://wainwrighted.herokuapp.com/https://asccw.playngonetwork.com/Casino/GameLoader?div=pngCasinoGame&pid=2&gid=bookofdead&lang=en_US&practice=1&demo=2&width=100%&height=100%&isbally=False&fullscreenmode=False&rccurrentsessiontime=0&rcintervaltime=0&autoplaylimits=0&autoplayreset=0&channel=desktop&callback=flashCallback&coreWebUrl=https://asccw.playngonetwork.com/&resourcelevel=0&hasJackpots=False&defaultsound=False&showPoweredBy=True');
                $api_url = $this->in_between('this.configuration.server = "', '"', $http);
                $new_api_endpoint = config('casino-dog.games.playngo.new_api_endpoint').$token.'/'.$select_session['game_id_original'].'/game_event';
                $changed_content = str_replace('this.configuration.server = "'.$api_url, 'this.configuration.server = "'.$new_api_endpoint.'?original='.$api_url, $http);
                $changed_content = str_replace('this.configuration.currency = ""', 'this.configuration.currency = "USD"', $changed_content);
                //$changed_content = str_replace('window.StatsHandler = new StatsHandler("bookofdead", "desktop", "", StatsHandler.Megaton, "en_GB", "1", "1");', '', $changed_content);
                //$changed_content = str_replace($api_url, $new_api_endpoint, $changed_content);
		        //$changed_content = str_replace('//</script>', '', $changed_content);
                //$changed_content = str_replace('//<script>', '', $changed_content);

                echo $changed_content;
            //$http = Http::get('https://fmtcw.playngonetwork.com/Casino/GameLoader?div=pngCasinoGame&pid=594&gid=aztecwarriorprincess&lang=en_US&practice=1&demo=2&isbally=False&fullscreenmode=False&rccurrentsessiontime=0&rcintervaltime=0&autoplaylimits=0&autoplayreset=0&channel=desktop&resourcelevel=0&hasJackpots=False&defaultsound=False&embedmode=iframe&origin=https://www.duxcasino.com&showPoweredBy=True');
            } else {
                $http = Http::get('https://ascflyp.playngonetwork.com/Casino/GameLoader');
                return $http;

            }
        }


    }

    /*
    * fake_iframe_url() used to display as src in iframe, this is only visual. If you have access to game aggregation you should generate a working session with game provider.
    *
    * @param string $slug
    * @param [type] $currency
    * @return void
    */
    public function fake_iframe_url(string $slug, $currency) {
        $game_id_purification = explode(':', $slug);
        if($game_id_purification[1]) {
            $game_id = $game_id_purification[1];
        }
        if($currency === 'DEMO' || $currency === 'FUN') {
            $build_url = 'https://bog.pragmaticplay.net/gs2c/openGame.do?gameSymbol='.$game_id.'&websiteUrl=https%3A%2F%2Fblueoceangaming.com&platform=WEB&jurisdiction=99&lang=en&cur='.$currency;
        }
        $build_url = 'https://bog.pragmaticplay.net/gs2c/html5Game.do?gameSymbol='.$game_id.'&websiteUrl=https%3A%2F%2Fblueoceangaming.com&platform=WEB&jurisdiction=99&lang=en&cur='.$currency;
        return $build_url;
    }

    /*
    * custom_entry_path() used for structuring the path the launcher is displayed on
    *
    * @param [type] $gid
    * @return void
    */
    public function custom_entry_path($gid)
    {
        $url = env('APP_URL')."/casino/ContainerLauncher";
        return $url;

    }

    /*
    * modify_game() used for replacing HTML content
    *
    * @param [type] $token_internal
    * @param [type] $game_content
    * @return void
    */
    public function modify_game($token_internal, $game_content)
    {
        //$select_session = $this->get_internal_session($token_internal);
        $gc = $game_content['html'];
        $gc = str_replace('../Casino/GameLoader', env('APP_URL').'/dynamic_asset/playngo/GameLoader.js;'.$token_internal, $gc);
        return $gc;
    }
}

