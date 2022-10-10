<?php
namespace Wainwright\CasinoDog\Controllers\Game\Bgaming;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Wainwright\CasinoDog\Controllers\Game\SessionsHandler;
use Wainwright\CasinoDog\CasinoDog;

class BgamingGameController
{
    # Disclaimer: this should be made into a job and/or contract on any type of high load

    public static function bgaming_gameid_transformer($game_id, $direction)
    {
        if($direction === 'explode') {
            try {
                $explode_game = explode('/', $game_id);
                $exploded_game_id = $explode_game[1];
                return $exploded_game_id;
            } catch (\Exception $exception) {
                Log::warning('Errored trying to transform & explode game_id on bgaming_gameid_transformer() function in bgamingcontroller.');
                return false;
            }
        } elseif($direction === 'concat') {
            $concat = 'softswiss/'.$game_id;
            return $concat;
        }
        Log::warning('Transform direction not supported, use concat or explode on bgaming_gameid_transformer().');
        return false;
    }s

    public static function freshPlaySession($game_id, $method, $user_agent) {

        if($method === 'demo_method') {
            $game_id = self::bgaming_gameid_transformer($game_id, 'explode');
            $url = 'https://bgaming-network.com/play/'.$game_id.'/FUN?server=demo';
            $http_get = Http::withHeaders($user_agent)->retry(2, 4000)->get($url);
            return $http_get;
        }
        // Add in additional grey methods here, specify the method on the internal session creation when a session is requested, don't split this here

        return 'generateSessionToken() method not supported';
    }

    public static function tryExistingPlaySession($game_id, $token_original, $user_agent)
    {
        $game_id = self::bgaming_gameid_transformer($game_id, 'explode');
        $url = 'https://bgaming-network.com/games/'.$game_id.'/FUN?play_token='.$token_original;
        $http_get = Http::withHeaders($user_agent)->retry(2, 4000)->get($url);
        return $http_get;
    }

    public static function demo_link($game_id)
    {
        $url = 'https://bgaming-network.com/play/'.$game_id.'/FUN?server=demo';
        return $url;
    }

    public static function modify_game($token_internal, $html)
    {
        $select_session = SessionsHandler::sessionData($token_internal);
        $new_api_endpoint = config('gameconfig.bgaming.new_api_endpoint').$token_internal.'/';  // building up the api endpoint we want to receive game events upon
        $replaceAPItoOurs = str_replace('https://bgaming-network.com/api/', $new_api_endpoint, $html);  // swap the legitimate game endpoint to ours
        //$replaceAPItoOurs = str_replace('sentry.softswiss.net', 'bog.asia', $replaceAPItoOurs); // sentry removal
        $replaceAPItoOurs = str_replace('googletagmanager.com', 'bog.asia', $replaceAPItoOurs); // remove googletagmanager.com
        $replaceAPItoOurs = str_replace('UA-98852510-1', ' ', $replaceAPItoOurs); // remove google analytics ID
        $replaceAPItoOurs = str_replace('FUN', $select_session['session_data']['currency'], $replaceAPItoOurs);
        $replaceAPItoOurs = str_replace('https://boost.bgaming-network.com/analytics.js', 'custom.js?game='.$select_session['session_data']['game_id'], $replaceAPItoOurs); // removing analytics script, however this is a new relic script you can use to use the 'frontend-cloudflare-workers' method
        $replaceAPItoOurs = str_replace('yes', 'utf-8', $replaceAPItoOurs); // sentry removal
        //$replaceAPItoOurs = str_replace('<body>', '<body>'.self::load_bundle(), $replaceAPItoOurs); // sentry removal
        //$replaceAPItoOurs = str_replace('document.write', ' ', $replaceAPItoOurs); // sentry removal
        //$replaceAPItoOurs = str_replace($origin_session_token, $token, $game_content);
        return $replaceAPItoOurs;
    }

    public static function selenium_retrieval($game_id)
    {
        $game_id = self::bgaming_gameid_transformer($game_id, 'explode');
        $url = 'https://bgaming-network.com/play/'.$game_id.'/FUN?server=demo';
        return $url;
    }

    public static function requestSession($session = NULL)
    {
        $proposed_session = $session; // validate this again if you multi-server setup between API & actual session creation jobs
        $select_session = SessionsHandler::sessionData($proposed_session['token_internal']);
        if($select_session === false or !$select_session['session_data']) { //internal session not found
               return false;
        }

        $player_id = $select_session['session_data']['player_id'];
        $token_internal = $select_session['session_data']['token_internal'];
        $game_id = $select_session['session_data']['game_id_original'];
        $user_agent = $select_session['session_data']['user_agent'] ?? '[]';
        $currency = $select_session['session_data']['currency'];
        $check_active_session = SessionsHandler::sessionFindPreviousActive($player_id, $token_internal, $game_id);

        if($select_session['session_data']['extra_meta']['launcher_behaviour'] === 'selenium_retrieval') {
            return self::selenium_retrieval($game_id);
        }

        if($check_active_session === false) {
            $retrieve_play_session = self::freshPlaySession($game_id, 'demo_method', $user_agent);
            if($retrieve_play_session->status() !== 200) {
                return false;
            }
        } else {
            $old_token_to_transfer = $check_active_session['token_original'];
            $retrieve_play_session = self::tryExistingPlaySession($game_id, $old_token_to_transfer, $user_agent);
            if($retrieve_play_session->status() !== 200) {
                $retrieve_play_session = self::freshPlaySession($game_id, 'demo_method', $user_agent);
                if($retrieve_play_session->status() !== 200) {
                    return false;
                }
            } else {
                SessionsHandler::sessionExpired($check_active_session['token_internal']);
            }
        }

        $game_content = $retrieve_play_session;
        $origin_session_token = CasinoDog::in_between('\"play_token\":\"', '\",\"', $game_content);

        if($origin_session_token === false)
        {
            Log::critical('Not being able to select play_token, even though the status & original game data seems correct. Possibly game source/structure has changed itself - disable game before proceeding to investigate thoroughly. '.json_encode($origin_session_token));
            return false;
        }

        SessionsHandler::sessionUpdate($token_internal, 'token_original', $origin_session_token); //update session table with the real game session token
        $change_content = self::modify_game($token_internal, $game_content);
        return $change_content;
    }

    public static function proxy_event($internal_token, $request) {
        $resp = ProxyHelperFacade::CreateProxy($request)->toHost('https://bgaming-network.com/api', 'api/respins.io/games/bgaming/'.$internal_token);
        return $resp;
    }
    public static function game_event($internal_token, $slug, Request $request)
    {
        $getSession = SessionsHandler::sessionData($internal_token);
        if($getSession) {
            $player_id = $getSession['session_data']['player_id'];
            $currency = $getSession['session_data']['currency'];
            $resp = self::proxy_event($internal_token, $request); // we fire the actual player request to game provider
            $data_origin = json_decode($resp->getContent(), true);

            if(isset($data_origin['api_version'])) {
                // This package supports "api_version 2" & "api_version 0", however some games are custom created, before production make sure to test each and every game thoroughly.
                // "Commands" below are on player side/game client.
                    if($request->command === 'init') {
                        // Init is initial load, though can also be intermediary, when you for example switch tabs or are inactive for a while
                        $data_origin['balance']['wallet'] = self::get_balance($internal_token);
                        $data_origin['options']['currency']['code'] = $getSession['session_data']['currency'];
                    } elseif($request->command === 'finish') {
                        // Finish command is similar to "payout" button on older slotmachines, because we are immediately adding/subtracting regardless spins what we do is a simple balance call
                        $data_origin['balance']['wallet'] = self::get_balance($internal_token, 'bypass_cache');
                        $data_origin['options']['currency']['code'] = $getSession['session_data']['currency'];
                    } elseif($request->command === 'spin') {
                        // Spin bet amount (bet minus) should probably be in front of the actual cURL to bgaming above, but as we don't pay any ggr anyway, we might aswell cancel it afterwards for ease
                        $betAmount = $data_origin['outcome']['bet'];
                        $winAmount = $data_origin['outcome']['win'];

                        if(isset($data_origin['flow']['purchased_feature']['name'])) {
                            if($data_origin['flow']['purchased_feature']['name'] === 'freespin_chance') {
                                $betAmount = $betAmount * 1.5;
                            }
                        }
                        if(isset($request['options']['purchased_feature'])) {
                            if($request['options']['purchased_feature'] === "freespin_buy") {
                                $betAmount = $request['options']['bet'] * 100;
                                $winAmount = $data_origin['outcome']['win'];
                            }
                        }
                        $data_origin['options']['currency']['code'] = $getSession['session_data']['currency'];
                        $data_origin['balance']['wallet'] = self::process_game($internal_token, $betAmount, $winAmount, $data_origin);
                    } elseif($request->command === 'freespin') {
                        $betAmount = $data_origin['outcome']['bet'];
                        $winAmount = $data_origin['outcome']['win'];
                        $data_origin['options']['currency']['code'] = $getSession['session_data']['currency'];
                        $data_origin['balance']['wallet'] = self::process_game($internal_token, 0, $winAmount, $data_origin);
                    }
                return response()->json($data_origin);

                } else { // API VERSION (bgaming side) !== 2
                    if($request->command === 'init') {
                        $data_origin['balance'] = self::get_balance($internal_token);
                        $data_origin['options']['currency']['code'] = $getSession['session_data']['currency'];
                    } elseif($request->command === 'finish') {
                        $data_origin['balance'] = self::get_balance($internal_token, 'bypass_cache');
                        $data_origin['options']['currency']['code'] = $getSession['session_data']['currency'];
                    } elseif($request->command === 'flip') {
                        // flip = heads or tails game
                                $betAmount = (int) $request['options']['bet'];
                                $winAmount = 0;

                                if(isset($data_origin['result']['total'])) {
                                    $winAmount =  $data_origin['result']['total'];
                                }
                                if(isset($data_origin['game']['state'])) {
                                    if($data_origin['game']['state'] === 'closed') {
                                    $data_origin['balance'] = self::process_game($internal_token, $betAmount, $winAmount, $data_origin);
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
                            $data_origin['balance'] = self::process_game($internal_token, $betAmount, $winAmount, $data_origin);

                    }
                    } else {
                        $data_origin['balance'] = self::get_balance($internal_token);
                        $data_origin['options']['currency']['code'] = $getSession['session_data']['currency'];
                    }
               return response()->json($data_origin);
        }
    } else {
            abort(404, 'Internal Session not found.');
    }

    }

    public static function get_balance($internal_token, $type = NULL)
    {
        if($type === NULL) {
            // Bgaming tends to use balance calls as a 'keepAlive' to check if session/player still playing or on-screen, every 1-2 seconds when player is idle.
            // If we would send operator callback (request the balance from the casino) this often for no reason, would be immense extra load (on actual decent traffic).
            // The solution is to cache the player balance for 60 seconds. This _should_ not give any issues, as the cache-key is based on internal token (game session) and is always unique.
            // Because we generate a new gamesession for every new entry, this means in-case a player would reload the game it has a new unique internal token thus cache no longer would be there.
            // We are 'pulling' (deleting) the cache-key on actual process_game() function so it can be set again.
            if(Cache::has('balance_bgaming:'.$internal_token)) {
                $balance = Cache::get('balance_bgaming:'.$internal_token);
                return (int) $balance['balance'];
            }

            $balance = OperatorController::operatorCallbacks($internal_token, 'balance');
            if(is_numeric($balance)) {
                $cache_data = [
                    'balance' => (int) $balance,
                    'time' => time(),
                ];
                Cache::put('balance_bgaming:'.$internal_token, $cache_data, 60); // storing 60 seconds balance
            }
            return (int) $balance;
        } elseif($type === 'bypass_cache') {
            $balance = OperatorController::operatorCallbacks($internal_token, 'balance');
            return (int) $balance;
        } else {
            abort(400, 'Error, type on get_balance() seems not used in BgamingController.php');
        }
    }

    public static function process_game($internal_token, $betAmount, $winAmount, $game_data, $type = NULL)
    {
        if($type === NULL) {
            $type = 'internal';
            $data = [
                'bet' => $betAmount,
                'win' => $winAmount,
                'game_data' => $game_data,
            ];
            $balance = OperatorController::operatorCallbacks($internal_token, 'game', $data);
            Cache::pull('balance_bgaming:'.$internal_token); //pulling cache key so user will not see cached (old) balance
            return (int) $balance;

            //if($currency === 'USD') {
                //$playerCurrentBalance = self::getBalance($internal_token);

                // To add error response for insufficient balance on bgaming
                //if($betAmount > $playerCurrentBalance) {
                //    abort(400, 'balance insufficient: '.$playerCurrentBalance.' bet: '.$betAmount);
                //}

                //$processBetCalculation = $playerCurrentBalance - $betAmount;
                //$processWinCalculation = $processBetCalculation + $winAmount;
                //$transformToOurBalanceFormat = floatval($processWinCalculation / 100);
                //$player->update(['balance_usd' => $transformToOurBalanceFormat]);

                //return $processWinCalculation;

            //} else {
            //    abort(400, 'balance not supported');
            //}
        } else {
            // Here we will add later on external balance/bet callbacks, outside of own system (for example i have in mind to make 'full api' & 'internal' mode)
            $type = $type;
        }
    }
}