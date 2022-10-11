<?php
namespace Wainwright\CasinoDog\Controllers\Game\PragmaticPlay;

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
class PragmaticPlayGame extends PragmaticPlayMain
{
    use GameKernelTrait;

    public function game_event(Request $request)
    {
        $internal_token = $request->internal_token;
        $action = $request->action;

        if($action === 'reloadBalance.do') {
            return $this->reloadBalance($internal_token, $request);

        } elseif($action === 'doInit') {
            return $this->doInit($internal_token, $request);

        } elseif($action === 'doSpin' || $action === 'doCollect' || $action === 'doWin' || $action === 'doDeal') {

        return $this->doSpin($internal_token, $request, $action);

        } elseif($action === 'saveSettings.do') {
            return $this->curl_request($request);
        } else {
            return $this->real_money_token();
        }

    }


    public function promo_event(Request $request)
    {
        $action = $request->action;

        if($action === 'unread') {
            return '{"error":0,"description":"OK","announcements":[]}';
        } elseif($action === 'active') {
            $symbol = $request->symbol;
            $mgckey = $this->real_money_token();
            $get = Http::get('https://aventonv-dk1.pragmaticplay.net/gs2c/promo/race/prizes?symbol='.$symbol.'&mgckey='.$mgckey);
            return $get;
        } else {
            return $this->real_money_token();
        }
    }

    public function real_money_token() {
        $cache_length = 120; // 120 seconds = 2 minutes

        if($cache_length === 0) {
            $real_url = $this->get_token_mrbit();
            $parse = $this->parse_query($real_url);
            $get_token = $parse['mgckey'];
            return $get_token;
        }
        $get_token = Cache::remember('pragmaticplay:realmoneytoken', $cache_length, function () {
            $real_url = $this->get_token_mrbit();
            $get_token = Cache::put('pragmaticplay:realurl', $real_url, 120);
            $parse = $this->parse_query($real_url);
            return $parse['mgckey'];
        });
        return $get_token;
    }

    public function reloadBalance($internal_token, Request $request) {
        $send_event = $this->curl_request($request);
        $query = $this->parse_query($send_event);
        $get_balance = $this->get_balance($internal_token) / 100;
        $query['balance'] = $get_balance;
        $query['balance_cash'] = $get_balance;
        $query = $this->build_response_query($query);
        return $query;
    }

    public function doInit($internal_token, Request $request) {
        $bridged_session_token = $this->create_new_bridge_session($internal_token, $request);

        $send_event = $this->curl_request($request);
        $query = $this->parse_query($send_event);
        $get_balance = $this->get_balance($internal_token) / 100;
        $query['balance'] = $get_balance;
        $query['balance_cash'] = $get_balance;
        $query['rtp'] = '1.00';
        //$query['gameInfo'] = '{props:{max_rnd_sim:"19230769",max_rnd_hr:"1",max_rnd_win:"200"}}';
        $query['cfgs'] = '2523';

        $bridge_init = str_replace('mgckey', 'mgckey='.$bridged_session_token.'&old_mgckey=', $request->getContent()); 
        $bridge_send = $this->curl_cloned_request($internal_token, $bridge_init, $request);
        $query = $this->build_response_query($query);


        return $query;
    }



    public function create_new_bridge_session($internal_token, Request $request) {
        $bridge_session = new PragmaticPlaySessions();
        $session = $bridge_session->fresh_game_session($request->symbol, 'token_only');
        $update_session = $this->update_session($internal_token, 'token_original_bridge', $session);
        Cache::put($session.':index', 2, now()->addHours(6));
        Cache::put($session.':counter', 3, now()->addHours(6));
        Cache::put($session.':balance', 10000000);

        return $update_session['data']['token_original_bridge'];
    }

    public function getAmount($money)
    {
        $cleanString = preg_replace('/([^0-9\.,])/i', '', $money);
        $onlyNumbersString = preg_replace('/([^0-9])/i', '', $money);
        $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;
        $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
        $removedThousandSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $stringWithCommaOrDot) * 100;
        return (float) str_replace(',', '.', $removedThousandSeparator);
    }

    public function doSpin($internal_token, Request $request, $isRespin = NULL) {
        $rand_internal_id = rand(0, 100000);
        $parent_session = $this->get_internal_session($internal_token);
        $token_original_bridge = $parent_session['data']['token_original_bridge'];
        $altered_win_request = $request->toArray();

        if(isset($altered_win_request['mgckey'])) {
            $altered_win_request['mgckey'] = $token_original_bridge;
        }
        if(isset($altered_win_request['index'])) {
            $altered_win_request['index'] = Cache::get($token_original_bridge.':index');
            $altered_win_request['counter'] = Cache::get($token_original_bridge.':counter');
        }

        $cloned_request = (clone $request)->replace($altered_win_request); // build a new request with existing original headers from player, we are only replacing body content
        $respin_send_event = $this->curl_request($cloned_request);
        $query = $this->parse_query($respin_send_event);

        Log::debug($rand_internal_id.' spin: request ---- '.json_encode($cloned_request));
        Log::debug($rand_internal_id.' spin: response ---- : '.json_encode($query));
        Log::debug($rand_internal_id.' spin: type '.$isRespin);

        $new_bridge_balance = $this->getAmount($query['balance']);
        $old_bridge_balance_cache = Cache::get($token_original_bridge.':balance');
        $old_bridge_index_cache = Cache::get($token_original_bridge.':index');
        $old_bridge_counter_cache = Cache::get($token_original_bridge.':counter');

        Log::debug($rand_internal_id.' spin: new_bridge_balance '.$new_bridge_balance);
        Log::debug($rand_internal_id.' spin: old_bridge_balance '.$old_bridge_balance_cache);

        $difference = (int) $new_bridge_balance - $old_bridge_balance_cache;

        Log::debug('winam: '.(int) $difference);

        if($difference > (int) 212121211) { // raw respin function set on integer netto win, if you put to 10000 it will respin on all results above 100$ by creating a new bridge session 
            Log::debug('>> '.$rand_internal_id.' spin: respin triggered on amount '.$difference);
            Cache::forget($token_original_bridge.':balance');
            $this->create_new_bridge_session($internal_token, $cloned_request);
            Log::debug($rand_internal_id.' spin: respin request ---- '.json_encode($cloned_request));
            Log::debug($rand_internal_id.' spin: respin response ---- : '.json_encode($respin_send_event));
            return $this->doSpin($internal_token, $cloned_request, 'respin from '.$rand_internal_id);
        }

        if($difference < 0) {
            $bet_amount = str_replace('-', '', $difference);
            $process_game = $this->process_game($internal_token, $bet_amount, 0, $query);
        } else {
            $win_amount = $difference;
            $process_game = $this->process_game($internal_token, 0, $win_amount, $query);
        }
        Log::debug('callback: '.(int) $process_game);

        $query['balance'] = $process_game / 100;
        $query['balance_cash'] = $process_game / 100;

        Cache::forget($token_original_bridge.':balance');
        Cache::put($token_original_bridge.':balance', $new_bridge_balance);
        Cache::forget($token_original_bridge.':index');
        Cache::put($token_original_bridge.':index', $old_bridge_index_cache + 1);
        Cache::forget($token_original_bridge.':counter');
        Cache::put($token_original_bridge.':counter', $old_bridge_counter_cache + 2);
        return $this->build_response_query($query);
    }

    public function old_game_mechanic()
    {
        $balance_call_needed = true;
        $bonus_active = false;

        if(isset($query['fs_total'])) { //payout bonus game
            $bonus_active = true;
            $win_amount = $query['tw'];
            $process_game = $this->process_game($internal_token, 0, $win_amount, $query);
            $query['balance'] = $process_game;
            $query['balance_cash'] = $process_game;
            return $this->build_response_query($query);
        }

        if(isset($query['fs'])) {
            $bonus_active = true;
            $fs = $query['fs'];

        if(isset($query['fs_bought'])) {
                if($fs === 1) {
                    $bet_amount = $query['c'] * $query['l'] * 100; // credit * lines * 100 (convert to 100 coin value)
                    $process_game = $this->process_game($internal_token, ($bet_amount * 100), 0, $query);
                    if(is_numeric($process_game)) {
                        $balance = $process_game / 100;
                        $query['balance'] = $balance;
                        $query['balance_cash'] = $balance;
                        return $this->build_response_query($query);
                    } else
                    { //throw insufficient balance error
                        if($process_game === '-1') {
                            return '-1&balance=-1&balance_cash=-1';
                        } else {
                            Log::notice('Unknown bet processing error occured: '.$request);
                            return 'unlogged'; // returning this will log out the session
                        }
                    }
                }
            }
        }

        if(isset($query['c'])) { // check if it's bet call
            if($query['na'] === 's') {
                $bet_amount = $query['c'] * $query['l'] * 100; // credit * lines * 100 (convert to 100 coin value)
                if($bonus_active === true) {
                    $bet_amount = 0;
                }
                $process_game = $this->process_game($internal_token, $bet_amount, 0, $query);
                $balance_call_needed = false;
                if(is_numeric($process_game)) {
                    $balance = $process_game / 100;
                } else
                { //throw insufficient balance error
                    if($process_game === '-1') {
                        return '-1&balance=-1&balance_cash=-1';
                    } else {
                        Log::notice('Unknown bet processing error occured: '.$request);
                        return 'unlogged'; // returning this will log out the session
                    }
                }
            }
        }

        if(isset($query['w'])) {
            $selectWinArgument = $query['w'];
            $winRaw = floatval($selectWinArgument);
            if($winRaw !== '0.00') {
                $win_amount = $query['w'] * 100;
                if($bonus_active === true) {
                    $win_amount = 0;
                }
                $process_game = $this->process_game($internal_token, 0, $win_amount, $query);
                $balance = $process_game / 100;
                $balance_call_needed = false;
            }
        }

        if($balance_call_needed === true) {
            $balance = $this->get_balance($internal_token) / 100;
        }

        $query['balance'] = $balance;
        $query['balance_cash'] = $balance;
        $query = $this->build_response_query($query);

        return $query;
    }


    public function build_response_query($query)
    {
        $resp = http_build_query($query);
        $resp = urldecode($resp);
        return $resp;
    }

    public function parse_query($query_string)
    {
        parse_str($query_string, $q_arr);
        return $q_arr;
    }

    public static function proxy_event($internal_token, $request) {
        $resp = ProxyHelperFacade::CreateProxy($request)->toHost('https://demogamesfree.pragmaticplay.net', 'api/games/pragmaticplay/'.$internal_token);
        return $resp;
    }

    public function curl_cloned_request($internal_token, $data, $request)
    {
        $internal_token = $request->segment(4);
        $url_explode = explode($internal_token, $request->fullUrl());
        $url = 'https://demogamesfree.pragmaticplay.net'.$url_explode[1];

        $response = Http::retry(1, 1500, function ($exception, $request) {
            return $exception instanceof ConnectionException;
        })->withBody(
            $data, 'application/x-www-form-urlencoded'
        )->post($url);

        return $response;
    }

    public function curl_request(Request $request)
    {
        $internal_token = $request->segment(4);
        $url_explode = explode($internal_token, $request->fullUrl());
        $url = 'https://demogamesfree.pragmaticplay.net'.$url_explode[1];
        $data = $request->getContent();

        $response = Http::retry(1, 1500, function ($exception, $request) {
            return $exception instanceof ConnectionException;
        })->withBody(
            $data, 'application/x-www-form-urlencoded'
        )->post($url);

        return $response;
    }


    public function get_token_mrbit() {
        $url = "https://mrbit.bet/api/games/CW_PPP_JohnHunterAndTheBookOfTut/play_real";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
        "authority: mrbit.bet",
        "accept: application/json, text/plain, */*",
        "accept-language: en-ZA,en;q=0.9",
        "cookie: sub_accounts=Vld3aHVGU005ZWtoTGtLVy9TS2NicFV1RzRtNzRxUDZaRkVNT1dUR2xNZ3ROWnlPcXJ6YVJqbkZFbnd4dm1lc09zcTJuSWVLYk5nMDE5dzVMNVg3dldIQ2dZR3RWNkJOTXRnY1VNMHNrQXRKYmgzYUZLLzJYSkdkWjErbkVEejlPR3hDQmtTTExoZjRHOC9iME1ZSVpwZ0J0U1ZuSEFVbU9qdTN3ZS9xL2tqVThIOXZEWU1LdFB6QVRFMWpJWjlSR25TeUJKd1VBUWlMaHFGRldpb1BpeFI5STRxd1N5cGNyTFE3ZW5SRkJHQUM4QTM2WVg2dXVWRERBc1lNd2RRbFp3bTQvb2l4OTYvTFR1WnlwNWh2UEdROWJTcjF1a1J3TG56NEVZUFZNL1c5YmdkOVQ2YlJFZlJDZjJZVEdMRjVaVXlGZi9uN0QvNHVxRkRobVJhWnBCMlY1MnlhNElqTXVETmZVdWhKZm5jRFRkZDE1QSt1WndOcmNxMVNGeFVEcXJHaCtpN0wzL2FuNnl2bDA1R2FQcUNtWTc4RHNPbkIxOVRSWkVMWnAwYz0tLVZNRGd3MXJmWjZ0Ym1FR3lLcVg5amc9PQ%3D%3D--5f7a07e288e9e1f8acf70125ffa9e69ec7385238; refcode=mb293517; encrypted_refcode=69b89cea4c501153dea82a88d8444e35; visit_url=https%3A%2F%2Fmrbit.bet%2Fen%3Fautologin_data%3D8ec9d745c0239b4044a58be744905d2050064e4b07d8e3a4a8ab547627fb09f61c148ed51c008a3572eb58dc25ce04fd5efc7225b70db9799662769c8728069777bfc87c0cdef7bbb6a1cc078148099975ae76f455cadf07a92a494ffd1507dca688d097946f9c33411800bdd4c132194de58e1ed33e10e4706cb0675a7d9628af12dca793bc84ca76d5bb614c1ea5b0832301dfe5ba4e282892b2ae8fabcb2e63ad7ee3a79338062935bc32d3ea0fbd%26autologin_iv%3Da7534bcf093c5f7266f85600ea006be3%26autologin_signature%3D50832673672d9c16b27605ccfa94450a486db4cc%26locale%3Den%26sud%3D0d92b457-2c0b-4d3e-ae39-fbba69d3ee69; s2s=; language=en; skip_registration_hint=true; _uId_cookie=ab017e32ece7d76b74c954516f3049c4; user_is_registered=true; geo=NL; seen_user_before=true; traffic-rules=aDF1ZXRlQ3E4SHpkSnQvMDBhWTRhZjh0STZ1cWdaVHFxb3JuVEI4MU5DaDkvbWZ5WHdLZlJ4TGlIZXdYVlo2MzZwMUhGU0RobXhGeHlKekFVSTV2ZllGUFJ5aC9KU1hybUJ6NHJrK2ZEdzdhY1pla2JHNVVqQmpMR1VzbTh4MWo4QXIrR2lnWnBEOFZtR092WEl4T1ZmVmQwaW9Pd1M1RXVNNVA1cmt4ZVlZVFUydmlDYXBJamxkS0hlREZzK0l5bGIza25sWU1qUVU3b01YejM4WGplQT09LS1lUXdTdmhub0Q5eXl6MXNQS083eS9RPT0%3D--3b69f9a5eb5f011468d7ca503db9fa0b03d9859e; vwo_identity_id=tTIHw4Ef6-9-Pk_AE8qTZ4uJu_V8h4pPeulQyZNzg20; argos_hash=Rb7cj1i9PnFTA0ZIn4uzqxNQXbY%3D; visited_at=1663906529; session_uuid=0d92b457-2c0b-4d3e-ae39-fbba69d3ee69; device_id=UnhhOiOw3X6U5118n7VPPCzb2pKUw0q7SgT2VgU-14U; locale=en; _vwo_uuid_v2=D5930FB4138011740A0A245DFB692AB08|0211bf1c756d20d0bf6ad91cea015981; _vwo_ssm=1; _vis_opt_s=1%7C; _vis_opt_test_cookie=1; _vwo_uuid=D5930FB4138011740A0A245DFB692AB08; _ga=GA1.2.1759930141.1663906540; _gid=GA1.2.1061124868.1663906540; token_id=96dfbf7a-6ff3-4cb5-a871-fc1d19475616; user_token=BAhJIikyN2FlNGRhZi1mNGU1LTQwOTYtYmVmMS1hMjI0MjZjNjVhYTAGOgZFVA%3D%3D--12774947c1b7415e6267d01914e3f434ba035b46; auth_token=27ae4daf-f4e5-4096-bef1-a22426c65aa0; _vwo_ds=3%3Aa_0%2Ct_0%3A0%241663906530%3A78.16856224%3A%3A13_0%2C12_0%2C11_0%2C10_0%2C9_0%2C8_0%2C7_0%3A31_0%2C29_0%2C24_0%2C3_0%2C2_0%3A23; _vwo_sn=0%3A14; _mr_bit_session=UGlyaXJ4UFBPV0ZQV0xBVWxKalFFSzRWeUZrQmxQRnNFblQxV1FUZGU2QUJ2dUJzdE9BNTFxeWN0YjFuSHJyWFVDOUtoenhlUVlPcExUbVd3a3pVeVdJYkY5U0xpc0JMdWxrSTd4QW1CRnFYVWJxc0FnaVRVbGpOQnpDYUVmRXhjejFsK3NnT0lHWDdJUmJvaUdVYkUrMjllUGliencvdE5FM0xEWlJoVWZjM2U0L0hHMjBsdDU3eWJYTlh3Q0lFaFBNYmw1TlZzRTlpU1lla1J4M1V4NCtVWWFWVEJFOHpnalQvK0dJSEFhcHhLZnBBSWNYNm82UFdSdVVVVjdQQTBsNEZXNlBJMVNVN2kwS3JlS0p0WWErdmxCT1ZiQkFMYW8zcWZRT1BwRzg9LS0xaVdDWWt0YlBEOWlhWFNnWS9vMWFBPT0%3D--48c543ebd5a0aaad19515a32ab199515b5f222fe; __cf_bm=FlSDPbBl9ExsMCC80oMGOEPj8q38kr6LDvsIyLMR1xk-1663907370-0-AQ9EU8zts0nVYFqW+1LaSmjR4ePt0u2DkuK3NVuTbUJBA0hqGMOMvzIRGPs6abTWF4RlBt2cEhFIVDL+F/OxEbY=",
        "sec-ch-ua-mobile: ?1",
        "sec-fetch-dest: empty",
        "sec-fetch-mode: cors",
        "sec-fetch-site: same-origin",
        "sentry-trace: 3f887eb8a5c7492da5e3d2656bd1d092-809671d341d34e68-0",
        "user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.102 Mobile Safari/537.36",
        "x-locale: en",
        "x-token: 27ae4daf-f4e5-4096-bef1-a22426c65aa0",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        $resp = json_decode($resp, true);
        $token = $resp['payload']['runnerOptions']['token'];

        $url = "https://api.atlantgaming.com/api/v2/launcher";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "authority: api.atlantgaming.com",
            "accept: */*",
            "accept-language: en-ZA,en;q=0.9",
            "content-type: text/plain;charset=UTF-8",
            "origin: https://launch.atlantgaming.com",
            "sec-ch-ua-mobile: ?1",
            "sec-fetch-dest: empty",
            "sec-fetch-mode: cors",
            "sec-fetch-site: same-site",
            "user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.102 Mobile Safari/537.36",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $data = '{"jsonrpc":"2.0","params":{"token":"'.$token.'"},"method":"sessions/launch"}';
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        $resp = json_decode($resp, true);
        $url = $resp['data']['url'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        $headers = [];
        $output = rtrim($output);
        $data = explode("\n",$output);
        $headers['status'] = $data[0];
        array_shift($data);

        foreach($data as $part){
            $middle = explode(":",$part,2);
            if ( !isset($middle[1]) ) { $middle[1] = null; }
            $headers[trim($middle[0])] = trim($middle[1]);
        }
        return $headers['location'];
        }
    }