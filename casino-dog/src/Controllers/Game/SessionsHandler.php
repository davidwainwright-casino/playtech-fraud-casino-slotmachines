<?php
namespace Wainwright\CasinoDog\Controllers\Game;
use Wainwright\CasinoDog\Controllers\Game\GameKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Wainwright\CasinoDog\Traits\ApiResponseHelper;
use Wainwright\CasinoDog\CasinoDog;
use Wainwright\CasinoDog\Controllers\Game\OperatorsController;
use Wainwright\CasinoDog\Controllers\DataController;
use Wainwright\CasinoDog\Models\ParentSessions;

class SessionsHandler extends GameKernel
{
    use ApiResponseHelper;

    public function entryWildcardDomain(Request $request) {
        $token = current(explode('.', $request->getHost()));
        $casino_dog = new CasinoDog();

        $select_session = SessionsHandler::sessionData($token);
        if($select_session === false) {
            $casino_dog->save_log('SessionsHandler()', 'Session ' . $request->token . ' not found.');
            return CasinoDog::errorRouting(404, 'Session '.$token.' not found.');
        } else {

            $token = $token;
            $player_id = $select_session['data']['player_id'];
            $entry_securekey = CasinoDog::generate_sign($token);
            return redirect('/g?token='.$token.'&entry='.$entry_securekey.'&player_id='.$player_id);

            return $this->session($token, $player_id, $entry_securekey, $request);
        }

    }

    public function entrySession(Request $request)
    {
        $casino_dog = new CasinoDog();

            $validate = $this->enterSessionValidation($request);
            if($validate->status() !== 200) {
                return $validate;
            }
            $player_id = $request->player_id;
            $token = $request->token;
            $entry_securekey = $request->entry;
            return $this->session($token, $player_id, $entry_securekey, $request);

    }

    public function session($token, $player_id, $entry_securekey, Request $request)
    {
        $agent = $request->header('user-agent');
        try {

        $select_session = SessionsHandler::sessionData($request->token);
        if($select_session === false) {
            $casino_dog->save_log('SessionsHandler()', 'Session ' . $request->token . ' not found.');
            return CasinoDog::errorRouting(404, 'Session not found.');
        }
        $verify_signature = CasinoDog::verify_sign($entry_securekey, $token);
        if($verify_signature === false) {
            return CasinoDog::errorRouting(403, 'Entry signature invalid, create new session.');
        }

        if($select_session['data']['expired_bool'] === 1) {
            return CasinoDog::errorRouting(400, 'Session expired, create new session.');
        }

        $session_state_update = SessionsHandler::sessionUpdate($request->token, 'state', 'SESSION_ENTRY');
        if($session_state_update === false) {
            return CasinoDog::errorRouting(400, 'Bad request. Not able to change session_state.');
        }

        $ua = $request->header('User-Agent');
        $set_user_agent = self::sessionUpdate($token, 'user_agent', array($ua)); //set user_agent from player to session

        $final_session_data = $session_state_update;
        $select_extra_meta = $final_session_data['extra_meta'];
        $game_controller = config('casino-dog.games.'.$select_extra_meta['provider'].'.controller');

        if(!$game_controller) {
            self::sessionFailed($token);
            return CasinoDog::errorRouting(400, 'Bad request. Failed to retrieve game controller, report to system admin.');
        }

        $game_launcher_behaviour = config('casino-dog.games.'.$select_extra_meta['provider'].'.launcher_behaviour');
        if(!$game_launcher_behaviour) {
            $casino_dog->save_log('SessionHandler()', 'No launcher behaviour specified for method. Either disable games or add launcher behaviour to config.php. Session: '.json_encode($final_session_data));
            self::sessionFailed($token);
            return CasinoDog::errorRouting(400, 'Bad request. No launcher behaviour specified.');
        }

        $game_controller = new $game_controller;
        $request_game_session = $game_controller->load_game_session($final_session_data);
        header('Access-Control-Allow-Origin: *');
        //header('X-Frame-Options: SAMEORIGIN');
        if($request_game_session === false) {
            self::sessionFailed($token);
            return CasinoDog::errorRouting(400, 'Error trying to retrieve origin game, please refresh.');
        }

        if($game_launcher_behaviour === 'redirect') {
            self::sessionUpdate($token, 'state', 'SESSION_STARTED');
            return redirect($request_game_session);
        }
        elseif($game_launcher_behaviour === 'internal_game') {
            self::sessionUpdate($token, 'state', 'SESSION_STARTED');
            //$request_game_session = preg_replace('/[\x00-\x1f\*]/', '', trim($request_game_session));
            $request_game_session = strtr(utf8_decode($request_game_session), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
            //var_dump($request_game_session);
            return $request_game_session;
        }
        else {
            self::sessionFailed($token);
            $casino_dog->save_log('SessionsHandler()', 'Unsupported launcher configuration, set to either internal_game or redirect within /config/casino-dog.php.', json_encode($final_session_data));
            return CasinoDog::errorRouting(400, 'Bad request. Unsupported launcher behaviour specified.');
        }
    } catch (\Exception $exception) {
        $casino_dog = new CasinoDog();
        $casino_dog->save_log('SessionsHandler', $exception->getMessage().' at line '.$exception->getLine().' in file '.$exception->getFile());
        return CasinoDog::errorRouting(400, 'Error trying to retrieve origin game, please refresh.');
    }
    }

    public static function sessionFailed($token)
    {
        try {
            SessionsHandler::sessionUpdate($token, 'state', 'SESSION_FAILED');
            SessionsHandler::sessionUpdate($token, 'expired_bool', 1);
            Cache::pull($token);
        } catch (\Exception $exception) {
            $casino_dog = new CasinoDog();
            $casino_dog->save_log('SessionsHandler()', 'Error trying to invalidate session at sessionFailed(). Token:'.$token);
        }
    }


    public static function sessionExpired($token)
    {
        try {
            SessionsHandler::sessionUpdate($token, 'state', 'SESSION_EXPIRED');
            SessionsHandler::sessionUpdate($token, 'expired_bool', 1);
            Cache::pull($token);
        } catch (\Exception $exception) {
            Log::warning('Error trying to expire session at sessionExpired(). Token:'.$token);
        }
    }

    # /api/respins.io/aggregation/createSession?game=softswiss:AlohaKingElvis&currency=USD&mode=real&player=croco&operator_key=96e9f726-74e3-4752-a0ca-7bcc758d7f43
    public static function createSession($data)
    {
        $operator_key = $data['operator_key'];
        $game = $data['game'];
        $request_ip = $data['request_ip'];
        $game_mode = $data['mode'];
        $operator_key = $data['operator_key']; // ^ to change to operator ID
        $currency = $data['currency'];
        $player_operator_id = $data['player'];

        $collection = collect(DataController::getGames());
        $select_game = $collection->where('slug', $game)->where('enabled', 1)->first();

        if(!$select_game) { // Game not found or enabled
            $search_disabled = $collection->where('slug', $game)->where('internal_enabled', 0)->first();
            if($search_disabled) {
                $prepareResponse = array('status' => 'error', 'message' => 'Game found, however this game is disabled.', 'request_ip' => $request_ip);
            } else {
                $prepareResponse = array('status' => 'error', 'message' => 'Game not found', 'request_ip' => $request_ip);
            }
            return $prepareResponse;
        }
        $owned_by = $data['ownedBy'] ?? '1';

        $extra_meta = [
            'provider' => $select_game->provider,
            'launcher_behaviour' => config('casino-dog.games.'.$select_game->provider.'.launcher_behaviour'),
            'mode' => $game_mode,
        ];
        $token_generation = Str::orderedUuid();
        /*
        $player_data = [
            'player_operator_id' => $player_operator_id,
            'operator_key' => $operator_key,
            'data' => [],
            'currency' => $currency,
            'ownedBy' => $owned_by,
        ];
        $player_id = PlayerController::findOrCreatePlayer($player_data);
        $player_id = $player_id['player_id'];
        */
        $player_id = hash_hmac('md5', $currency.'*'.$player_operator_id, $operator_key);
        $invalidate_previous_init = self::invalidatePrev($player_operator_id, $operator_key);
        if($invalidate_previous_init === false) { // Return error, as for some reason we were unable to invalidate previous sessions
            $prepareResponse = array('status' => 'error', 'message' => 'Critical error, please contact your account manager ASAP. Try using different player_id.', 'request_ip' => $request_ip);
            return $prepareResponse;
        }

        $prepend_session_object = array(
            'player_id' => $player_id,
            'player_operator_id' => $player_operator_id,
            'operator_id' => $operator_key,
            'game_id' => $select_game->slug,
            'game_provider' => $select_game->provider,
            'extra_meta' => json_encode($extra_meta),
            'user_agent' => '[]',
            'token_internal' => $token_generation,
            'currency' => $currency,
            'game_id_original' => $select_game->gid,
            'token_original' => 0,
            'token_original_bridge' => 0,
            'expired_bool' => 0,
            'state' => 'SESSION_INIT',
            'created_at' => now(),
            'updated_at' => now(),
        );

        $insert = ParentSessions::insert($prepend_session_object);
        $store_in_cache = Cache::put($token_generation, $prepend_session_object, now()->addMinutes(60)); //storing session in cache however still use fallover on db, memcached is preferred for game handling under high load, see OPTIMIZATIONS.MD
        Log::debug($store_in_cache);
        $entry_signature = CasinoDog::generate_sign($token_generation);
        $session_url = env('APP_URL').'/g?token='.$token_generation.'&entry='.$entry_signature.'&player_id='.$player_operator_id;

        $game_controller = config('casino-dog.games.'.$select_game->provider.'.controller');
        $game_controller = new $game_controller;
        if(config('casino-dog.games.'.$select_game->provider.'.fake_iframe_url') === 0) {
            $fake_url = $session_url;
        } elseif($game_controller === NULL) {
            $fake_url = NULL;
        } else {
            $fake_url = $game_controller->fake_iframe_url($select_game->slug, $currency);
        }

        if(config('casino-dog.games.'.$select_game->provider.'.custom_entry_path') !== 0) {
            $session_url = $game_controller->custom_entry_path($select_game->gid).'?token='.$token_generation.'&entry='.$entry_signature.'&player_id='.$player_operator_id;
        }


        $prepareResponse = array(
            'status' => 'success',
            'message' => array(
                'data' => $prepend_session_object,
                'session_url' => $session_url,
                'fake_iframe_url' => $fake_url
            ),
            'request_ip' => $request_ip
        );
        return $prepareResponse;
    }

    public static function invalidatePrev($player)
    {
        try {
            ParentSessions::where('player_id', $player)
            ->where('expired_bool', 0)
            ->where('state', 'SESSION_INIT')
            ->update([
               'state' => 'SESSION_OVERRULE_INVALIDATION',
               'expired_bool' => 1,
            ]);
        } catch (\Exception $exception) {
            Log::critical('Error trying to invalidate older sessions, this should never error. Investigate:'.$exception);
            return false;
        }

         return true;
    }

    public static function sessionFindPreviousActive($player_id, $token_internal, $game_id_original)
    {
        $find = ParentSessions::where('player_id', $player_id)
            ->where('game_id_original', $game_id_original)
            ->where('expired_bool', 0)
            ->where('token_original', '!=', 0)
            ->first();
        if(!$find) {
            return false;
        } else {
            return $find;
        }
    }

    public static function sessionData($token_internal)
    {
        $retrieve_session_from_cache = Cache::get($token_internal);
        if ($retrieve_session_from_cache) {
            $response_data = array('data_retrieval_method' => 'cache', 'data' => $retrieve_session_from_cache);
        } elseif(!$retrieve_session_from_cache) {
            $retrieve_session_from_database = ParentSessions::where('token_internal', $token_internal)->first();
            if($retrieve_session_from_database) {
                $store_in_cache = Cache::put($token_internal, $retrieve_session_from_database, now()->addMinutes(60)); //storing session in cache however still use fallover on db, memcached is preferred for game handling under high load, see OPTIMIZATIONS.MD
                $response_data = array('data_retrieval_method' => 'database', 'data' => $retrieve_session_from_database);
            }
        } else {
            return false; //session not found, neither in cache as in database - create appriopate action in place you are dialing
        }
        return $response_data ?? false;
    }

    public static function sessionUpdate($token_internal, $key, $newValue)
    {
        $retrieve_session_from_database = ParentSessions::where('token_internal', $token_internal)->first();

        if(!$retrieve_session_from_database) {
            //Session not found
            return false;
        }
        try {
            $new = $retrieve_session_from_database->update([
                $key => $newValue
            ]);
        } catch (\Exception $exception) {
            Log::critical('Database error, most likely you are trying to update a non existing key/field, or cache is mismatched (token: '.$token_internal.') - clearing this key. Investigate asap. Error: '.json_encode($exception));
            Cache::pull($token_internal);
            return false;
        }
        $data = $retrieve_session_from_database;
        $data[$key] = $newValue;

        $store_in_cache = Cache::put($token_internal, $data, now()->addMinutes(120));
        return $data;
    }

    public function enterSessionValidation(Request $request) {
        $validator = Validator::make($request->all(), [
            'entry' => ['required', 'min:10', 'max:100'],
            'token' => ['required', 'min:10', 'max:100'],
            'player_id' => ['required', 'min:3', 'max:100'],
        ]);

        $ip = $_SERVER['REMOTE_ADDR'];
        if($ip === NULL || !$ip) {
            $ip = $request->header('CF-Connecting-IP');
            if($ip === NULL) {
              $ip = $request->ip();
            }
        }

        if ($validator->stopOnFirstFailure()->fails()) {
            $errorReason = $validator->errors()->first();
            $prepareResponse = array('message' => $errorReason, 'request_ip' => $ip);
            return $prepareResponse;
        }

        return $this->respondOk();

    }


}
