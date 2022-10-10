<?php
namespace Wainwright\CasinoDog\Controllers\Game\Bgaming;

use Illuminate\Http\Request;
use Wainwright\CasinoDog\Controllers\Game\SessionsHandler;
use Wainwright\CasinoDog\Controllers\Game\Bgaming\BgamingMeeps;
use Wainwright\CasinoDog\Controllers\Game\Bgaming\BgamingMain;
use Wainwright\CasinoDog\Facades\ProxyHelperFacade;
use Wainwright\CasinoDog\Controllers\Game\GameKernel;
use Wainwright\CasinoDog\Controllers\Game\OperatorsController;

class BgamingGame extends BgamingMain
{
    public static function proxy_event($internal_token, $request) {
        $resp = ProxyHelperFacade::CreateProxy($request)->toHost('https://bgaming-network.com/api', 'api/games/bgaming/'.$internal_token);
        return $resp;
    }

    public static function curl_request($internal_token, $request)
    {
        $url = "https://bgaming-network.com/api/".$request->segment(5)."/".$request->segment(6)."/".$request->segment(7);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
           "authority: bgaming-network.com",
           "accept: */*",
           "accept-language: en-ZA,en;q=0.9",
           "content-type: application/json",
           "origin: https://bgaming-network.com",
           "referer: https://bgaming-network.com/games/ZorroWildHeart/FUN?play_token=aef60b43-533d-45e4-8013-1737a55f39cd",
           "sec-fetch-dest: empty",
           "sec-fetch-mode: cors",
           "sec-fetch-site: same-origin",
           "sec-gpc: 1",
           "user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.102 Safari/537.36",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $data = $request->getContent();
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }

    public function game_event(Request $request)
    {
        try {
            $urlFullUrl = $request->fullUrl();
            $kernel = new GameKernel;
            $internal_token = $request->segment(4);
            $select_session = $kernel->get_internal_session($internal_token);
            $getSession = SessionsHandler::sessionData($internal_token);

            if($getSession) {
                $player_id = $getSession['data']['player_id'];
                $currency = $getSession['data']['currency'];
                $game_id = $select_session['data']['game_id'];

                $freespin_kernel = new BgamingMeeps;
                $active_bonus_check = $freespin_kernel->freespin_check_active($player_id, $game_id, $request->command);

                if($active_bonus_check['status'] === 'active_bonus_session') {
                    $from_db = 1;
                    if($request->command === 'init') {
                        $data_origin = $active_bonus_check['init_event'];
                    }
                    if($request->command === 'freespin') {
                        $data_origin = $active_bonus_check['game_event'];
                    }
                } else {
                    $from_db = 0;
                    $resp = self::proxy_event($internal_token, $request); // we fire the actual player request to game provider
                    $data_origin = json_decode($resp->getContent(), true);
                }

                if(isset($data_origin['errors'])) {
                    $kernel->expire_internal_session($internal_token);
                    return $data_origin;
                }

                if(isset($data_origin['api_version'])) {
                    // This package supports "api_version 2" & "api_version 0", however some games are custom created, before production make sure to test each and every game thoroughly.
                    // "Commands" below are on player side/game client.
                        if($request->command === 'init' || $request->command === 'close') {
                            // Init is initial load, though can also be intermediary, when you for example switch tabs or are inactive for a while
                            $data_origin['balance']['wallet'] = self::get_balance($internal_token);
                            $data_origin['options']['currency']['code'] = $getSession['data']['currency'];
                        } elseif($request->command === 'finish') {
                            // Finish command is similar to "payout" button on older slotmachines, because we are immediately adding/subtracting regardless on results instead, so what we do is a simple balance call instead of "paying out"
                            $data_origin['balance']['wallet'] = self::get_balance($internal_token, 'bypass_cache');
                            $data_origin['options']['currency']['code'] = $getSession['data']['currency'];
                        } elseif($request->command === 'spin') {
                            // Spin bet amount (bet minus) should probably be in front of the actual cURL to bgaming above, but as we don't pay any ggr anyway, we might aswell cancel it afterwards for ease
                            $betAmount = $data_origin['outcome']['bet'];
                            $winAmount = $data_origin['outcome']['win'];
                            if(isset($data_origin['outcome']['freespins_issueddddddddd'])) // <<<DISABLED>>>
                            { // freespins limiter, first detect if free spins feature unlocked (bonus)
                                $freespin_issued_amount = $data_origin['outcome']['freespins_issued'];
                                if($freespin_issued_amount > 0 && $from_db === 0) { // if player has issued freespins
                                    $freespin_kernel_new = new BgamingMeeps;
                                    $freespin_module = $freespin_kernel_new->freespinner($internal_token, $freespin_issued_amount, $betAmount, $request); // fetch complete bonus result
                                    $data_origin = $freespin_module;
                                    $betAmount = $data_origin['outcome']['bet'];
                                    $winAmount = $data_origin['outcome']['win'];
                                }
                            }

                            if(isset($data_origin['flow']['purchased_feature']['name'])) {
                                if($data_origin['flow']['purchased_feature']['name'] === 'freespin_chance') {
                                    $betAmount = $betAmount * 1.5;
                                }
                            }
                            if(isset($request['options']['purchased_feature'])) {
                                if($from_db === 0){
                                    if($request['options']['purchased_feature'] === "freespin_buy") {
                                        $betAmount = $request['options']['bet'] * 100;
                                        $winAmount = $data_origin['outcome']['win'];
                                    }
                                }
                            }
                            $data_origin['options']['currency']['code'] = $getSession['data']['currency'];
                            $data_origin['balance']['wallet'] = $this->process_game($internal_token, $betAmount, $winAmount, $data_origin);
                        } elseif($request->command === 'freespin') {
                            $betAmount = $data_origin['outcome']['bet'];
                            $winAmount = $data_origin['outcome']['win'];
                            $data_origin['options']['currency']['code'] = $getSession['data']['currency'];
                            $data_origin['balance']['wallet'] = $this->process_game($internal_token, 0, $winAmount, $data_origin);
                        }
                    return $data_origin;

                    } else { // API VERSION (bgaming side) !== 2
                        if($request->command === 'init') {
                            $data_origin['balance'] = self::get_balance($internal_token);
                            $data_origin['options']['currency']['code'] = $getSession['data']['currency'];
                        } elseif($request->command === 'finish') {
                            $data_origin['balance'] = self::get_balance($internal_token, 'bypass_cache');
                            $data_origin['options']['currency']['code'] = $getSession['data']['currency'];
                        } elseif($request->command === 'flip') {
                            // flip = heads or tails game
                                    $betAmount = (int) $request['options']['bet'];
                                    $winAmount = 0;

                                    if(isset($data_origin['result']['total'])) {
                                        $winAmount =  $data_origin['result']['total'];
                                    }
                                    if(isset($data_origin['game']['state'])) {
                                        if($data_origin['game']['state'] === 'closed') {
                                        $data_origin['balance'] = $this->process_game($internal_token, $betAmount, $winAmount, $data_origin);
                                        }
                                    }
                        } elseif($request->command === 'spin') {
                        // Old BGAMING api, where you can set individual betlines when placing bet (wager per betline * total amount of betlines)
                        if(isset($request['extra_data'])) {
                                $multiplier = count($request['options']['bets']);
                                $betAmount = (int) $multiplier * $request['options']['bets']['0'];
                                $winAmount = 0;

                                if(isset($data_origin['result']['total'])) {
                                    $winAmount = $data_origin['result']['total'];
                                }
                                //$winAmount = $data_origin['result']['total'];
                                $data_origin['balance'] = $this->process_game($internal_token, $betAmount, $winAmount, $data_origin);

                        }
                        } else {
                            $data_origin['balance'] = self::get_balance($internal_token);
                            $data_origin['options']['currency']['code'] = $getSession['data']['currency'];
                        }
                return $data_origin;
            }
            } else {
                    abort(404, 'Internal Session not found.');
            }
        } catch(\Exception $e) {
            echo "Error:\n ". $e->getmessage()."\n\n";
            echo "File:\n ". str_replace('__DIR__', ' ', $e->getfile())."\n\n";
            echo "\nThe exception was created on line:\n".$e->getLine();
            return;
        }
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