<?php
namespace Wainwright\CasinoDog\Controllers\Game\Oaks;

use Illuminate\Support\Facades\Http;
use Wainwright\CasinoDog\Facades\ProxyHelperFacade;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Support\Facades\Cache;

class OaksGame extends OaksMain
{
    use GameKernelTrait;


    public function game_event($request)
    {
        $url = $_SERVER['REQUEST_URI'];
        $url = explode('origin_url=', $url);
        $replace_url = str_replace('/&gsc', '/?gsc', $url[1]);
        $final_url = $replace_url;


	    $data_origin = $this->curl_request($final_url, $request);
        $data_origin = json_decode($data_origin, true);
        $internal_token = $request->internal_token;
        $select_session = $this->get_internal_session($internal_token)['data'];
        $oaks_session_id = $data_origin['session_id'];

        if($request->gsc === 'sync') {
          $get_cached_balance = Cache::get('oaks:sync:balance-'.$oaks_session_id);
          if($get_cached_balance) {
            $data_origin['user']['balance'] = $get_cached_balance;
            $data_origin['user']['currency'] = $select_session['currency'];
          } else {
            $balance = (int) $this->get_balance($internal_token);
            Cache::put('oaks:sync:balance-'.$oaks_session_id, $balance, 60);
            $data_origin['user']['balance'] = $balance;
            $data_origin['user']['currency'] = $select_session['currency'];
          }
          return $data_origin;
        }

        $balance_call_needed = 1;

        if($request->gsc === 'play') {
            $respin = false;

            if(isset($data_origin['context'])) {
                if(isset($data_origin['context']['spins'])) {
                    $round_bet = 0;
                    $round_win = 0;
                    $process_game_needed = 0;
                    if(isset($data_origin['context']['spins']['round_bet'])) {
                        $round_bet = $data_origin['context']['spins']['round_bet'];
                        if($round_bet > 0) {
                            $process_game_needed = 1;
                        }
                    }

                    if(isset($data_origin['context']['spins']['round_win'])) {
                        $round_win = $data_origin['context']['spins']['round_win'];
                        if($round_win > 0) {
                            $process_game_needed = 1;
                        }
                    }

                    if($process_game_needed === 1) {
                        $balance_call_needed = 0;
                        if($round_win > 1) { // respin from history
                            if($data_origin['context']['round_finished'] === true && $data_origin['origin_data']['feature'] === false) {
                                $respin_data = $this->retrieve_game_respins_template($select_session['game_id'], 'normal');
                                if($respin_data !== NULL) {
                                    $respin = true;
                                    $respin_decode = json_decode($respin_data, true);
                                    $data = json_encode($data_origin);
                                    $respin_decode['request_id'] = $data_origin['request_id'];
                                    $respin_decode['session_id'] = $data_origin['session_id'];
                                    $respin_decode['context']['last_win'] = $data_origin['context']['last_win'];
                                    $respin_decode['context']['spins']['round_bet'] = $data_origin['context']['spins']['round_bet'];
                                    $respin_decode['context']['spins']['bet_per_line'] = $data_origin['context']['spins']['bet_per_line'];
                                    $respin_decode['respin_win'] = $data_origin['context']['spins']['round_win'] ?? 'not_found';
                                    $respin_decode['user']['huid'] = $data_origin['user']['huid'] ?? 'not_found';
                                    //$respin_decode['user']['balance_version'] = $data_origin['user']['balance_version'] ?? 'not_found';
                                    $round_bet = $data_origin['context']['spins']['round_bet'];
                                    //$respin_decode['respin_bet'] = $data_origin['context']['spins']['round_bet'] ?? 'not_found';
                                    $data_origin = $respin_decode;  
                                    $round_win = 0;
                                }
                            }
                        }
                        $process_game = $this->process_game($internal_token, $round_bet, $round_win, $data_origin);
                        Cache::put('oaks:sync:balance-'.$oaks_session_id, $process_game, 60);
                    }
                    if($data_origin['context']['spins']['round_win'] === 0 && $data_origin['context']['spins']['round_bet'] > 10 && $data_origin['context']['spins']['total_win'] === 0) {
                        if($respin === false) {
                            if($data_origin['context']['round_finished'] === true && $data_origin['origin_data']['feature'] === false) {
                                $this->save_game_respins_template($select_session['game_id'], json_encode($data_origin), 'normal');
                            }
                        }
                    }

                }
            }
        }

        if(isset($data_origin['user'])) {
            if($balance_call_needed === 1) {
                $balance = (int) $this->get_balance($internal_token);
                Cache::put('oaks:sync:balance-'.$oaks_session_id, $balance, 60);
                $data_origin['user']['balance'] = $balance;
                $data_origin['user']['currency'] = $select_session['currency'];
            } else {
                $data_origin['user']['balance'] = $process_game;
                $data_origin['user']['currency'] = $select_session['currency'];
            }
        }

        //array_push($data_origin['status'], ['respin' => $respin]);
        $data_origin['respin'] = $respin ?? false;
        usleep('200000'); // 0.1s sleep/delay added to game if under 0.3s

        return $data_origin;
    }



    public function curl_modified_request($url, $data, $request)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
        "authority: betman-demo.head.3oaks.com",
        "accept: */*",
        "accept-language: en-ZA,en;q=0.9",
        "content-type: text/plain",
        "refferer: https://3oaks.com",
        "sec-ch-ua-mobile: ?0",
        "sec-fetch-dest: empty",
        "origin: https://3oaks.com",
        "sec-fetch-mode: cors",
        "sec-fetch-site: same-site",
        "user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.5195.127 Safari/537.36",
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

        $resp = ProxyHelperFacade::CreateProxy($request)->toUrl($url);

        return $resp;
    }

    public function replaceInBetweenDataset($a, $b, $replace_from_data, $replace_in_data)
    {
        $value_from = $this->in_between($a, $b, $replace_from_data);
        $value_in = $this->in_between($a, $b, $replace_in_data);
        return str_replace($value_in, $value_from, $replace_in_data);
    }

    public function replaceInBetweenValue($a, $b, $data, $value)
    {
        $value_from = $this->in_between($a, $b, $data);
        return str_replace($value_from, $value, $data);
    }
    public function curl_request($url, $request)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
        "authority: betman-demo.head.3oaks.com",
        "accept: */*",
        "accept-language: en-ZA,en;q=0.9",
        "content-type: text/plain",
        "refferer: https://3oaks.com",
        "sec-ch-ua-mobile: ?0",
        "sec-fetch-dest: empty",
        "origin: https://3oaks.com",
        "sec-fetch-mode: cors",
        "sec-fetch-site: same-site",
        "user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.5195.127 Safari/537.36",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $data = $request->getContent();
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        $resp = curl_exec($curl);
        curl_close($curl);

        return $resp;

        $resp = ProxyHelperFacade::CreateProxy($request)->toUrl($url);

        return $resp;
    }
}
