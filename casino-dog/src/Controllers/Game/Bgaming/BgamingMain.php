<?php
namespace Wainwright\CasinoDog\Controllers\Game\Bgaming;

use Illuminate\Support\Facades\Cache;
use Wainwright\CasinoDog\Controllers\Game\SessionsHandler;
use Wainwright\CasinoDog\CasinoDog;
use Wainwright\CasinoDog\Controllers\Game\GameKernel;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Http\Request;
use Cookie;
use Illuminate\Support\Str;

class BgamingMain extends GameKernel
{
    use GameKernelTrait;

    public function load_game_session($data) {
        $token = $data['token_internal'];
        $session = new BgamingSessions();
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
        return view('wainwright::launcher-content')
        ->with('game_content', $game_content['html'])
        ->with('cookie', "'".$game_content['cookie']."'");
    }

    /*
    * game_event() is where direct API requests from inside games are received
    *
    * @param Request $request
    * @return void
    */
    public function game_event(Request $request) {
        $event = new BgamingGame();
        return response()->json($event->game_event($request), 200);
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
    * dynamic_asset() used to load altered javascript
    *
    * @param string $asset_name
    * @param Request $request
    * @return void
    */
    public function dynamic_asset(string $asset_name, Request $request) {
        if($asset_name === 'analytics.js') {
            return $this->pretendResponseIsFile(__DIR__.'/AssetStorage/analytics.js', 'application/javascript; charset=utf-8');
        }

        if($asset_name === 'custom.js') {
            $response = "window.localStorage.clear();";
            return $response;
        }
    }

    /*
    * fake_iframe_url() used to display as src in iframe, this is only visual. If you have access to game aggregation you should generate a working session with game provider.
    *
    * @param string $slug
    * @param [type] $currency
    * @return void
    */
    public function fake_iframe_url($slug, $currency) {
        $game_id_purification = explode(':', $slug);
        if($game_id_purification[1]) {
            $game_id = $game_id_purification[1];
        }
        if($currency === 'DEMO' || $currency === 'FUN') {
            $build_url = 'https://bgaming-network.com/play/'.$game_id.'/FUN?server=demo';
        }
        $build_url = 'https://bgaming-network.com/games/'.$game_id.'/'.$currency.'?play_token='.Str::uuid();
        return $build_url;
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
        $select_session = $this->get_internal_session($token_internal);
        $new_api_endpoint = config('casino-dog.games.bgaming.new_api_endpoint').$token_internal.'/';  // building up the api endpoint we want to receive game events upon

        $gc = $game_content;
        $gc = str_replace('https://bgaming-network-mga.com/api/', $new_api_endpoint, $gc);  // swap the legitimate game endpoint to ours
        $gc = str_replace('https://bgaming-network.com/api/', $new_api_endpoint, $gc);  // swap the legitimate game endpoint to ours
        $gc = str_replace('googletagmanager.com', 'bogged.', $gc); // remove googletagmanager.com
        $gc = str_replace('FUN', $select_session['data']['currency'], $gc); // change curerncy
        $gc = str_replace('https://boost.bgaming-network.com/analytics.js', 'dynamic_asset/bgaming/analytics.js?game='.$select_session['data']['game_id'], $gc);
        $gc = str_replace('cdn.bgaming-network.com', 'wainwrighted.herokuapp.com/https://cdn.bgaming-network.com', $gc); // change curerncy



        return $gc;
    }
}

