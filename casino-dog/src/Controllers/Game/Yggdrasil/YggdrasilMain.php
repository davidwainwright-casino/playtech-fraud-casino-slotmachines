<?php
namespace Wainwright\CasinoDog\Controllers\Game\Yggdrasil;

use Wainwright\CasinoDog\Controllers\Game\GameKernel;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Http\Request;

class YggdrasilMain extends GameKernel
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
        $session = new YggdrasilSessions();
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
        return view('wainwright::launcher-content-yggdrasil')->with('game_content', $game_content);
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
	    $replacement_api_url = str_replace('https://', '', env('APP_URL'));
        $final = str_replace('netentff-game.casinomodule.com', $replacement_api_url.'%2Fapi%2Fgames%2Fnetent%2F'.$token, $url[1]);
        return redirect($final);
    }

    /*
    * game_event() is where direct API requests from inside games are received
    *
    * @param Request $request
    * @return void
    */
    public function game_event(Request $request) {
        $event = new YggdrasilGame();
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
    public function dynamic_asset($game, $game_code, $slug, Request $request) {

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
        $select_session = $this->get_internal_session($token_internal)['data'];
        $new_api_endpoint = config('casino-dog.games.yggdrasil.new_api_endpoint').$token_internal.'/';  // building up the api endpoint we want to receive game events upon


        $gc = $game_content;

        $gc = str_replace('pc/partnerconnect', 'https://wainwrighted.herokuapp.com/https://staticdemo.yggdrasilgaming.com/pc/partnerconnect', $gc);

        //$gc = str_replace('base href="', 'base href="https://wainwrighted.herokuapp.com/', $gc);

        //$gc = str_replace('server-fra.pff-ygg.com', $new_api_endpoint.'game/play?origin_url=', $gc);
        //$gc = str_replace('"use_cdn": true', '"use_cdn": false', $gc);
        //$gc = str_replace('"log_url": "', '"log_url": "https://wainwrighted.herokuapp.com/', $gc);

        //$gc = str_replace('"client_url": "', '"client_url": "https://wainwrighted.herokuapp.com/', $gc);

        return $gc;

    }
}

