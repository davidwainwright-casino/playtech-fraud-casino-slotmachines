<?php
namespace Wainwright\CasinoDog\Controllers\Game\iSoftbet;

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

class iSoftbetGame extends iSoftbetMain
{
    use GameKernelTrait;

    public function parse_query($query_string)
    {
        parse_str($query_string, $q_arr);
        return $q_arr;
    }

    public function build_response_query($query)
    {
        $resp = http_build_query($query);
        $resp = urldecode($resp);
        return $resp;
    }

    public function proxy_event($request) {
        $resp = ProxyHelperFacade::CreateProxy($request)->toHost('stage-games-lux.isoftbet.com/play_isb', 'win.radio.fm/play_isb');
        return $resp;
    }


    public function game_event(Request $request) {
        {

        $get = $this->curl_request($request);
        $data_origin = json_decode($get, true);
        if(isset($data_origin['rootdata']['data'])) {
            if(isset($data_origin['rootdata']['data']['balance'])) {
                $balance_call_needed = 1;
                if(isset($data_origin['rootdata']['data']['endGame'])) {
                    if($data_origin['rootdata']['data']['endGame']['bet'] > 0) {
                        $balance_call_needed = 0;
                        $bet = $data_origin['rootdata']['data']['endGame']['bet'];
                        $process_game = $this->process_game('974ddd71-b617-4273-b90b-d4ff42b49205', $bet, 0, $data_origin);
                        $data_origin['rootdata']['data']['endGame']['money'] = $process_game;
                    }

                }
                if(isset($data_origin['rootdata']['data']['doubleWin'])) {
                    if($data_origin['rootdata']['data']['doubleWin']['totalWinnings'] > 0) {
                        $balance_call_needed = 0;
                        $win = $data_origin['rootdata']['data']['doubleWin']['totalWinnings'];
                        $process_game = $this->process_game('974ddd71-b617-4273-b90b-d4ff42b49205', 0, $win, $data_origin);
                    }
                    $data_origin['rootdata']['data']['doubleWin']['money'] = $process_game;
                }
                $data_origin['rootdata']['data']['balance']['cashBalance'] = $this->get_balance('974ddd71-b617-4273-b90b-d4ff42b49205');
            }
        }
        return $data_origin;
    }

    public function curl_request(Request $request)
    {

        $url = 'https://stage-games-lux.isoftbet.com/play_isb';
        //$url = 'https://games-lux.isoftbet.com/play_isb';
        //$url = 'https://games-aws2.isoftbet.com/play_isb';
        $response = Http::retry(3, 500, function ($exception, $request) {
            return $exception instanceof ConnectionException;
        })->withBody(
            $request->getContent(), 'application/x-www-form-urlencoded'
        )->post($url);

        return $response;
    }

    public function get_balance($internal_token, $type = NULL)
    {
        $type = 'internal';
        $data = [
            'game_data' => 'balance_call',
        ];
        $balance = OperatorsController::operatorCallbacks($internal_token, 'balance', $data);
        return (int) $balance;
    }

    public static function process_game($internal_token, $betAmount, $winAmount, $game_data, $type = NULL)
    {
        $type = 'internal';
        $data = [
            'bet' => $betAmount,
            'win' => $winAmount,
            'game_data' => $game_data,
        ];
        $balance = OperatorsController::operatorCallbacks($internal_token, 'game', $data);
        return (int) $balance;
    }
}