<?php
namespace Wainwright\CasinoDog\Controllers\Game\Platipus;

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

class PlatipusGame extends PlatipusMain
{
    use GameKernelTrait;

    public function game_event($internal_token, $game, $action, Request $request)
    {
        $get = $this->curl_request($internal_token, $game, $action, $request);
        $data_origin = json_decode($get->getContent(), true);
        $win = 0;
        $bet = 0;
        $balance_call_needed = 1;

        if(isset($data_origin['bet'])) {
            $bet = ($data_origin['bet'] * 10) * 100;
            if($bet > 0) {
                $balance_call_needed = 0;
                $process_game = $this->process_game($internal_token, $bet, 0, $data_origin);
                $data_origin['currency'] = "USD";
                $data_origin['balance'] = $process_game / 100;
            }
        }

        if(isset($data_origin['win'])) {
            $win = $data_origin['win'] * 100;
            if($win > 0) {
                $balance_call_needed = 0;
                $process_game = $this->process_game($internal_token, 0, $win, $data_origin);
                $data_origin['balance'] = $process_game / 100;
            }
        }

        $data_origin['currency'] = "USD";
        if($balance_call_needed === 1) {
            $data_origin['balance'] = $this->get_balance($internal_token);
        }

        return $data_origin;
    }

    public function set_bridge_session($internal_token) {
        $select_session = $this->get_internal_session($internal_token)['data'];
        $session = new PlatipusSessions();
        $game_content = $session->fresh_game_session($select_session['game_id_original'], 'demo_method');
        $update_session = $this->update_session($internal_token, 'token_original_bridge', $game_content['token_original']);
        return $game_content['token_original'];
    }

    public function curl_request($internal_token, $game, $action, Request $request)
    {
        $select_session = $this->get_internal_session($internal_token)['data'];
        if($select_session['token_original_bridge'] === 0) {
            $this->set_bridge_session($internal_token);
            $select_session = $this->get_internal_session($internal_token)['data'];
        }
        try {
        $url = 'https://betconstruct.platipusgaming.com/onlinecasino/games/'.$game.'/'.$action.'?key='.$select_session['token_original_bridge'].'&requestType=json&fairtech=1';
        $resp = ProxyHelperFacade::CreateProxy($request)->toUrl($url);
        } catch(\Exception $e) {
            Log::emergency('Error on game callback on '.$url.': '.$e->getMessage());
            $this->set_bridge_session($internal_token);
            $select_session = $this->get_internal_session($internal_token)['data'];
            $url = 'https://betconstruct.platipusgaming.com/onlinecasino/games/'.$game.'/'.$action.'?key='.$select_session['token_original_bridge'].'&requestType=json&fairtech=1';
            $resp = ProxyHelperFacade::CreateProxy($request)->toUrl($url);
        }
        return $resp;
    }

}
