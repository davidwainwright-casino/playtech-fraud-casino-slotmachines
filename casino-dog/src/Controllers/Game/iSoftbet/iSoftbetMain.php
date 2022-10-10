<?php
namespace Wainwright\CasinoDog\Controllers\Game\iSoftbet;

use Wainwright\CasinoDog\Controllers\Game\GameKernel;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class iSoftbetMain extends GameKernel
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
        $session = new iSoftbetSessions();
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
        return view('wainwright::launcher-content-isoftbet')->with('game_content', $game_content);
    }


    /*
    * game_event() is where direct API requests from inside games are received
    *
    * @param Request $request
    * @return void
    */
    public function game_event(Request $request) {
        $event = new iSoftbetGame();
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
    public function dynamic_asset(string $asset_name, Request $request) {

        if($asset_name === 'config.json') {
            header('Content-Type: application/json; charset=UTF-8', true);
            $url = $request->url;
            $url = str_replace('@', '', $request->url);
            $http = Http::get($url);

            echo '{"ipCheck":{"status":0,"ip":"85.148.48.222","reason":{"ipRange":0,"country":0,"game":0,"licensee":0,"operator":0,"provider":0,"region":0,"city":0}},"maintenance":{"status":0},"connectionDetails":{"server":"win.radio.fm","port":5865,"proxy":7514}}';
        }

        if($asset_name === 'type.xml') {
            $url = $request->url;
            $url = str_replace('@', '', $request->url);
            $http = Http::get($url);
            header('Content-Type: application/xml; charset=UTF-8', true);

            if (str_contains($url, 'settings.xml')) {
                $http_get = str_replace('"serverAddress" value="stage-games-lux.isoftbet.com"', '"serverAddress" value="win.radio.fm"', $http);
                $http_get = str_replace('"serverAddress" value="games-lux.isoftbet.com"', '"serverAddress" value="win.radio.fm"', $http);
                echo $http_get;
            } else {
                echo $http;
            }
        }
        if($asset_name === 'type.css') {
            $url = $request->url;
            $url = str_replace('@', '', $request->url);
            $http = Http::get($url);
            header('Content-Type: text/css; charset=UTF-8', true);
            echo $http;
        }


        if($asset_name === 'js.js') {
            $url = $request->url;
            $url = str_replace('@', '', $request->url);
            $http = Http::get($url);


            header('Content-Type: application/javascript; charset=UTF-8', true);
        if (str_contains($url, '.core.js')) {
                $http_get = str_replace('u.Device.ASSET_PATH_BASE', '"https://win.radio.fm/dynamic_asset/isoftbet/type.xml?url=@" + u.Device.ASSET_PATH_BASE', $http);
                $http_get = str_replace('c.Device.ASSET_PATH_BASE', '"https://win.radio.fm/dynamic_asset/isoftbet/type.xml?url=@" + c.Device.ASSET_PATH_BASE', $http);
                $http_get = str_replace('soundManifest:', 'soundManifest: "https://win.radio.fm/dynamic_asset/isoftbet/type.xml?url=@" + ', $http_get);
                //$http_get = str_replace('"&licenseeId="', '"&licenseeId=1016&like="', $http_get);
                //$http_get = str_replace('a.uid', 'location.href.s plit("__")[1] + ("__") + a.uid', $http_get);
                $http_get = str_replace('this.composeURL', '"https://win.radio.fm/dynamic_asset/isoftbet/config.json?url=@" + this.composeURL', $http_get);
                $http_get = str_replace('stage-games-lux.isoftbet.com/play_isb', 'win.radio.fm/play_isb', $http_get);
                $http_get = str_replace('games-lux.isoftbet.com/play_isb', 'win.radio.fm/play_isb', $http_get);
                $http_get = str_replace('games-aws2.isoftbet.com/play_isb', 'win.radio.fm/play_isb', $http_get);

                $http_get = str_replace('optionalSoundManifest:', 'optionalSoundManifest: "https://win.radio.fm/dynamic_asset/isoftbet/type.xml?url=@" + ', $http_get);
                $http_get = str_replace('dtSoundManifest:', 'dtSoundManifest: "https://win.radio.fm/dynamic_asset/isoftbet/type.xml?url=@" + ', $http_get);
                $http_get = str_replace(('c + "/play_isb'), ('c + "/play_isb'), $http_get);
                $http_get = str_replace(('l + "/play_isb'), ('l + "/play_isb'), $http_get);
                $http_get = str_replace(('gms-data.isoftbet.com'), ('corsanywhere.herokuapp.com/gms-data.isoftbet.com'), $http_get);

                echo $http_get;
            } elseif (str_contains($url, '.utils.js')) {
                $explode_static = explode("/js/", $url);
                $http_get = str_replace('ASSET_PATH_BASE=location.href.substring(window.location.href.split("?")[0].lastIndexOf("/")+1).split("_game.").map(String)[0]+"/"', 'ASSET_PATH_BASE="'.$explode_static[0].'/"', $http);
                echo $http_get;
            } else {
                echo $http;
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
    * modify_game() used for replacing HTML content
    *
    * @param [type] $token_internal
    * @param [type] $game_content
    * @return void
    */
    public function modify_game($token_internal, $game_content)
    {
        $select_session = $this->get_internal_session($token_internal);
        $link = explode($game_content['identifier'].'.html', $game_content['link']);
        $gc = $game_content['html'];
        $gc = str_replace('pt" src="', 'pt" src="dynamic_asset/isoftbet/js.js?url=@'.$game_content['static_url'], $gc);
        $gc = str_replace('css" href="', 'pt" src="dynamic_asset/isoftbet/type.css?url=@'.$game_content['static_url'], $gc);

        return $gc;
    }
}

