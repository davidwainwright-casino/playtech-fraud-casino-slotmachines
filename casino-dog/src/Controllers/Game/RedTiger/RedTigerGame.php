<?php
namespace Wainwright\CasinoDog\Controllers\Game\RedTiger;

use Illuminate\Support\Facades\Http;
use Wainwright\CasinoDog\Facades\ProxyHelperFacade;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Support\Facades\Cache;

class RedTigerGame extends RedTigerMain
{
    use GameKernelTrait;

    public function game_event($request)
    {
        $url = $_SERVER['REQUEST_URI'];
        $url = explode('game/', $url);
        $final_url = 'https://gserver-softswiss.redtiger.cash/softswiss/platform/game/'.$url[1];
        $http = $this->proxied_event($final_url, $request);
        $data_origin = json_decode($http->getContent(), true);
        $internal_token = $request->internal_token;
        $select_session = $this->get_internal_session($internal_token)['data'];

        $balance_call_needed = 1;

        if(isset($data_origin['result'])) {
            if(isset($data_origin['result']['game']['win'])) {
                $round_bet = 0;
                $round_win = 0;
                $process_game_needed = 0;
                if(isset($data_origin['result']['game']['stake'])) {
                    $round_bet = $data_origin['result']['game']['stake'] * 100;
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


    public function proxied_event($url, $request) 
    {
        $resp = ProxyHelperFacade::CreateProxy($request)->toUrl($url);
        return $resp;
    }
}
