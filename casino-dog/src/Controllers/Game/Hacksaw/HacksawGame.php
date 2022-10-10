<?php
namespace Wainwright\CasinoDog\Controllers\Game\Hacksaw;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Wainwright\CasinoDog\Facades\ProxyHelperFacade;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Http\Client\ConnectionException;
use Wainwright\CasinoDog\Controllers\Game\GameKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Wainwright\CasinoDog\Controllers\Game\OperatorsController;

class HacksawGame extends HacksawMain
{
    use GameKernelTrait;

    public function game_event($request)
    {
        $internal_token = $request->internal_token;
        $select_session = $this->get_internal_session($internal_token)['data'];

        $url = str_replace('games/hacksaw/'.$internal_token.'/'.$select_session['game_id_original'].'/', '', $request->fullUrl());
        $url = str_replace('win.radio.fm/api', 'rgs-demo.hacksawgaming.com/api', $url);

        $send_request = $this->curl_request($url, $request);
        if ($request->isMethod('post')) {
            $data_origin = json_decode($send_request->getContent(), true);
            //$data_origin['sessionUuid'] = $internal_token;
            $balance_call_needed = 1;
            $data_origin['name'] = $internal_token;
            $data_origin['displaySessionTimer'] = true;
            $data_origin['displayNetPosition'] = true;
            $data_origin['accountBalance']['currencyCode'] = "USD";
            if($request->bets) {
                $bets = $request->bets;
                $bet = (int) $bets[0]['betAmount'];
                if($request->buyBonus) {
                    $buy_bonus = $request->buyBonus;
                }
                if($bet > 0) {

                    $balance_call_needed = 0;
                    $process_game = $this->process_game($internal_token, $bet, 0, $data_origin);
                    $data_origin['accountBalance']['balance'] = $process_game;
                }
            }

            if(isset($data_origin['round'])) {
                if(isset($data_origin['round']['events'])) {
                    if(isset($data_origin['round']['events'][0])) {
                        if(isset($data_origin['round']['events'][1])) {
                            foreach($data_origin['round']['events'] as $game_event) {
                            }
                        }
                        $win = (int) $data_origin['round']['events'][0]['wa'];

                        if($win > 0) {
                            $balance_call_needed = 0;
                            $process_game = $this->process_game($internal_token, 0, $win, $data_origin);
                            $data_origin['accountBalance']['balance'] = $process_game;
                        }
                    }
                }
            }
            if($balance_call_needed === 1) {
            $data_origin['accountBalance']['balance'] = $this->get_balance($internal_token);
            }

            return $data_origin;
        }

        return $send_request;
    }

    public function curl_request($url, $request)
    {
        $resp = ProxyHelperFacade::CreateProxy($request)->toUrl($url);

        return $resp;
    }
}
