<?php
namespace Wainwright\CasinoDog\Controllers\Game\Bgaming;

use Illuminate\Http\Request;
use Wainwright\CasinoDog\Controllers\Game\GameKernel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Wainwright\CasinoDog\Facades\ProxyHelperFacade;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Support\Collection;
use Wainwright\CasinoDog\Controllers\Game\Bgaming\Models\BgamingBonusGames;

class BgamingMeeps
{
    use GameKernelTrait;
    public function freespinner_config()
    {
        $config = [
            'all' => [
                'max_win' => 100000, // max_wix OVERRIDES THE BY_MULTIPLIER CATEGORY. To prevent endless loops this is disabled on 'buy feature'.
                'by_multiplier' => [
                    'small' => [
                        'to_bet_amount' => 100, // to which bet_amount is considered small sized
                        'multiplier' => 1000, // max multiplier (totalBonusWin / betAmount = multiplier)
                    ],
                    'medium' => [
                        'to_bet_amount' => 500, // to which bet_amount is considered medium sized
                        'multiplier' => 400, // max multiplier (totalBonusWin / betAmount = multiplier)
                    ],
                    'high' => [
                        'to_bet_amount' => 10000, // to which bet_amount is considered high sized
                        'multiplier' => 200, // max multiplier (totalBonusWin / betAmount = multiplier)
                    ],
                    'whale' => [ // this category is used for any betamount that surpasses the "high" category
                        'multiplier' => 50, // max multiplier (totalBonusWin / betAmount = multiplier)
                    ],
                ]
            ],
            'currency_overrides' => [// simply duplicate below to override specific currencies, however a better longterm solution is really to normalize all bets to 1 currency on exchange rate
                // currency-layer.com offers very cheap & accurate exchange rate API service and I would recommend, I am trying to limit external dependencies as much possible in the base
                'IDR' => [
                    'max_win' => 100000, // max_wix OVERRIDES THE BY_MULTIPLIER CATEGORY. To prevent endless loops this is disabled on 'buy feature'.
                    'by_multiplier' => [
                        'small' => [
                            'to_bet_amount' => 100, // to which bet_amount is considered small sized
                            'multiplier' => 1000, // max multiplier (totalBonusWin / betAmount = multiplier)
                        ],
                        'medium' => [
                            'to_bet_amount' => 500, // to which bet_amount is considered medium sized
                            'multiplier' => 400, // max multiplier (totalBonusWin / betAmount = multiplier)
                        ],
                        'high' => [
                            'to_bet_amount' => 10000, // to which bet_amount is considered high sized
                            'multiplier' => 200, // max multiplier (totalBonusWin / betAmount = multiplier)
                        ],
                        'whale' => [ // this category is used for any betamount that surpasses the "high" category
                            'multiplier' => 50, // max multiplier (totalBonusWin / betAmount = multiplier)
                        ],
                    ],
                ],
            ],
        ];
       return $config;
    }

    public static function proxy_event($internal_token, $request) {
        $resp = ProxyHelperFacade::CreateProxy($request)->toHost('https://bgaming-network-mga.com/api', 'api/games/bgaming/'.$internal_token);
        return $resp;
    }

    public function freespinner_loop(Request $new_request, $bonusgame_token, $urlReplaceToReal, $loop_amount)
    {
        try {
            $init = [
                'command' => 'init',
            ];
            $init_request = (clone $new_request)->replace($init); // build a new request with existing original headers from player, we are only replacing body content

        for ($i = 0; $i < 20; $i++){
            $resp = ProxyHelperFacade::CreateProxy($new_request)->toUrl($urlReplaceToReal);
            usleep(rand(100, 350)); //random wait inbetween request 100ms to 500ms
            $resp_init = ProxyHelperFacade::CreateProxy($init_request)->toUrl($urlReplaceToReal);
            $resp_to_array = json_decode($resp->getContent(), true);
            $resp_init_to_array = json_decode($resp_init->getContent(), true);
            if($resp_to_array['features']['freespins_left'] === 0) {
                break;
            }
            $new = new BgamingBonusGames();
            $new->bonusgame_token = $bonusgame_token;
            $new->game_id = 1;
            $new->player_id = 1;
            $new->replayed = false;
            $new->active = 0;
            $new->game_event = $resp_to_array;
            $new->init_event = $resp_init_to_array;
            $new->created_at = time();
            $new->updated_at = time();
            $new->save();
            usleep(125); //random wait inbetween request 100ms to 500ms
        }
        $report = [
            'status' => 'success',
            'bonusgame_token' => $bonusgame_token,
            'round_id' => $resp_to_array['flow']['round_id'],
            'current_fs' => $resp_to_array['features']['freespins_left'], //should be 0 fs else retrigger
            'game_state' => $resp_to_array['flow']['state'], // should be "closed" most of time, unless api_v1 where there is collect/finish command
            'win_amount' => $resp_to_array['balance']['game'],// total sum in cents ("100 coins value")
        ];
        Log::debug($report);
        return $report;

        } catch(\Exception $e) {
            //BgamingBonusGames::where('bonusgame_token', $bonusgame_token)->delete();
            Log::critical('Error freespinner_loop() '.$e->getMessage());
            $report = [
                'status' => 'error',
                'bonusgame_token' => $bonusgame_token,
                'current_fs' => 0,
                'error_message' => $e->getMessage(),
            ];
            return $report;
        }
    }

    public function freespinner_skip_check($currency, $betAmount, $win_amount) {
        $config = $this->freespinner_config();
        $parsed_config = $config['all'];
        $overriden_by_currency = false;
        if(isset($config['currency_overrides'][$currency])) {
            $parsed_config = $config['currency_overrides'][$currency];
            $overriden_by_currency = true;
        }
        if($betAmount > $parsed_config['by_multiplier']['high']['to_bet_amount']) {
           $bet_size_category = 'whale';
        } elseif($betAmount > $parsed_config['by_multiplier']['medium']['to_bet_amount']) {
            $bet_size_category = 'high';
        } elseif($betAmount > $parsed_config['by_multiplier']['small']['to_bet_amount']) {
           $bet_size_category = 'medium';
        } else {
            $bet_size_category = 'small';
        }
        $win_multiplier = $win_amount / $betAmount;
        if($win_multiplier > $parsed_config['by_multiplier'][$bet_size_category]['multiplier']) {
            $win_multiplier_triggered = true;
        } else {
            $win_multiplier_triggered = false;
        }
        if($betAmount > $parsed_config['max_win']) {
            $max_win_triggered = true;
        } else {
            $max_win_triggered = false;
        }
        $report = [ // please note that we are returning this report internally and is not leading - for example max win is not used on 'buy_feature' spins. This is simply to "categorize" the bet properly
            'bet_size_category' => $bet_size_category,
            'max_win_trigger' => $max_win_triggered,
            'multiplier_trigger' => $win_multiplier_triggered,
            'win_multiplier' => $win_multiplier,
            'overriden_by_currency_config' => $overriden_by_currency,
        ];
        return $report;
    }

    public function freespinner_liftoff($freespin_issued_amount, $betAmount, $url, Request $request)
    {
        $data = [
            "command" => "freespin",
            "options" => [
              "bet" => $betAmount
            ],
        ];
        $bonusgame_token = md5($url.now());
        $new_request = (clone request())->replace($data); // build a new request with existing original headers from player, we are only replacing body content
        $lift_off = $this->freespinner_loop($new_request, $bonusgame_token, $url, $freespin_issued_amount); // send loop
        $status = $lift_off['status'];
        $win_amount = 0;
        if($status === 'success') {
            $win_amount = $lift_off['win_amount'];
        }
        return [
            'status' => $status,
            'bonusgame_token' => $bonusgame_token,
            'win_amount' => $win_amount,
        ];
    }

    public function freespinner_respin_normal_spin(Request $request, $betAmount, $urlReplaceToReal) { // function for regular spin
        $spin = [
            "command" => "spin",
            "options" => [
              "bet" => $betAmount
            ],
        ];
        $finish_request = (clone $request)->replace($spin);
        $resp = ProxyHelperFacade::CreateProxy($finish_request)->toUrl($urlReplaceToReal);
        return $resp;
    }

    public function freespinner($internal_token, $freespin_issued_amount, $betAmount, Request $request)
    {
        $urlFullUrl = $request->fullUrl();
        $url = str_replace(env('APP_URL').'/api/game_tunnel/bgaming/', 'https://bgaming-network.com/api/', $urlFullUrl);
        $liftOff = $this->freespinner_liftoff($freespin_issued_amount, $betAmount, $url, $request);
        $kernel = new GameKernel;
        $select_session = $kernel->get_internal_session($internal_token);
        $player_id = $select_session['data']['player_id'];
        $currency = $select_session['data']['currency'];
        $game_id = $select_session['data']['game_id'];
        BgamingBonusGames::where('bonusgame_token', $liftOff['bonusgame_token'])->update([
            'player_id' => $player_id,
            'game_id' => $game_id,
        ]);
        $skip_check = $this->freespinner_skip_check($currency, $betAmount, $liftOff['win_amount']);
        Cache::put($player_id.$game_id, $liftOff['bonusgame_token'], now()->addMinutes(60));
        //$resp = $this->freespinner_respin_normal_spin($request, $betAmount, $url);
        return Cache::get($liftOff['bonusgame_token']);
    }

    public function freespin_check_active($player_id, $game_id, $command)
    {
        $bonus_from_cache = BgamingBonusGames::where('player_id', $player_id)->where('game_id', $game_id)->where('replayed', 0)->first();
        Log::notice($command.'Freespin_check_active'.$bonus_from_cache);
        if(!$bonus_from_cache) {
            return [
                'status' => 'no',
            ];
        } else {
            $init_event = $bonus_from_cache->init_event;
            BgamingBonusGames::where('id', $bonus_from_cache->id)->update([
                'replayed' => true,
            ]);
            $game_event = $bonus_from_cache->game_event;
            if($command === 'freespin' || $command === 'spin') {
                return [
                    'status' => 'active_bonus_session',
                    'init_event' => $init_event,
                    'game_event' => $game_event,
                ];
            } elseif($command === 'init') {
                return [
                    'status' => 'active_bonus_session',
                    'init_event' => $init_event,
                    'game_event' => $game_event,
                ];

            } else {
                return [
                    'status' => 'command not found',
                ];
            }
        }
    }
}
