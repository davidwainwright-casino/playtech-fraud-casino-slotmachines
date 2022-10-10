<?php
namespace Wainwright\CasinoDog\Controllers\Game\Platipus;

use Wainwright\CasinoDog\Facades\ProxyHelperFacade;
use Wainwright\CasinoDog\Controllers\Game\GameKernel;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PlatipusMain extends GameKernel
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
        $session = new PlatipusSessions();
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
        return view('wainwright::launcher-content-platipus')->with('game_content', $game_content);
    }


    /*
    * game_event() is where direct API requests from inside games are received
    *
    * @param Request $request
    * @return void
    */
    public function game_event($internal_token, $game, $action, Request $request) {
        $event = new PlatipusGame();
        return $event->game_event($internal_token, $game, $action, $request);
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
    public function dynamic_asset($game, $game_code, $slug, Request $request) {
        $http = 'https://wainwrighted.herokuapp.com/https://platipusgaming2.cloud/'.$game.'/'.$game_code.'/'.$slug;
	header('Content-Type: application/x-javascript');

	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        $resp = ProxyHelperFacade::CreateProxy($request)->toUrl($http);

        return $resp;
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
        $session = new PlatipusSessions();
        $game_content = $session->fresh_game_session($gid, 'demo_method');
        $game_content = str_replace('https://platipusgaming2.cloud/', '', $game_content['static_path']);
        $game_content = str_replace('https://platipusgaming.cloud/', '', $game_content);
        $url = env('APP_URL')."/platipus/".$game_content."g";
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
        $gc = $game_content['html'];

        $gc = str_replace('src="',  'src="'.$game_content['static_path'], $gc);
        $gc = str_replace('href="',  'href="'.$game_content['static_path'], $gc);

        return $gc;
    }
}

