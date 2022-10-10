<?php
namespace Wainwright\CasinoDog\Controllers\Game\Yggdrasil;

use Illuminate\Support\Facades\Http;
use Wainwright\CasinoDog\Facades\ProxyHelperFacade;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class YggdrasilGame extends YggdrasilMain
{
    use GameKernelTrait;

    public function game_event(Request $request)
    {
        $url = $_SERVER['REQUEST_URI'];
        $url = explode('game.web/', $url);
        $final_url = 'https://wainwrighted.herokuapp.com/https://demo.yggdrasilgaming.com/game.web/'.$url[1];
        if(str_contains($final_url, 'service?fn=clientinfo')) {
        $http = $this->curl_request($final_url, $request);
        return $http;
        } else {
        $http = $this->proxied_event($final_url, $request);
        return $http;
        }
        $data_origin = json_decode($http->getContent(), true);
        $internal_token = $request->internal_token;
        $select_session = $this->get_internal_session($internal_token)['data'];

        $balance_call_needed = 1;

        if(isset($data_origin['result'])) {
            if(isset($data_origin['result']['game']['win'])) {
                $round_bet = 0;
                $round_win = 0;
                $process_game_needed = 0;
                if(isset($data_origin['result']['game']['win']['stake'])) {
                    $round_bet = $data_origin['result']['game']['win']['stake'] * 100;
                    if($round_bet > 0) {
                        $process_game_needed = 1;
                    }
                }

                if(isset($data_origin['result']['game']['win']['total'])) {
                    $round_win = $data_origin['result']['game']['win']['total'] * 100;
                    if($round_win > 0) {
                        $process_game_needed = 1;
                    }
                }

                if($process_game_needed === 1) {
                    $balance_call_needed = 0;
                    $process_game = $this->process_game($internal_token, $round_bet, $round_win, $data_origin);
                }

            }

        if(isset($data_origin['result']['user'])) {
            if($balance_call_needed === 1) {
                if($request->action === 'settings') {
                $balance = (int) $this->get_balance($internal_token);
                $data_origin['result']['user']['balance']['cash'] = ($balance / 100);
                } else {
                $balance = (int) $this->get_balance($internal_token);
                $data_origin['result']['user']['balance']['cash']['atEnd'] = $balance / 100;
                }
            } else {
                $data_origin['result']['user']['balance']['cash']['atStart'] = floatval(($process_game - ($round_win)) / 100);
                $data_origin['result']['user']['balance']['cash']['afterBet'] =  floatval(($process_game - ($round_win - $round_bet)) / 100);
                $data_origin['result']['user']['balance']['cash']['atEnd'] = floatval(($process_game / 100));

            }
        }
        }

        return $data_origin;
    }

    public function curl_request($url, Request $request)
    {


    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $header_data = [
        'game_id' => $request->header('GAME-ID'),
        'authorization' => $request->header('Authorization'),
        'crid' => $request->header('X-CRID'),
        'csid' => $request->header('X-CSID'),
    ];

    $data = $request->getContent();

    $headers = array(
    "authority: demo.yggdrasilgaming.com",
    "accept: */*",
    "accept-language: en-ZA,en;q=0.9",
    "authorization: ".$header_data['authorization'],
    "game-id: ".$header_data['game_id'],
    "origin: https://staticdemo.yggdrasilgaming.com",
    "sec-ch-ua-mobile: ?0",
    "sec-fetch-dest: empty",
    "sec-fetch-mode: cors",
    "sec-fetch-site: same-site",
    "user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.5195.127 Safari/537.36",
    "x-crid: ".$header_data['crid'],
    "x-csid: ".$header_data['csid'],
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);


    $resp = curl_exec($curl);
    curl_close($curl);
    return $resp;


    }

    public function proxied_event($url, $request) 
    {
        $resp = ProxyHelperFacade::CreateProxy($request)->toUrl($url);
        return $resp;
    }
}
