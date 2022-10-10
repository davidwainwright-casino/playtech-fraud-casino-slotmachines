<?php
namespace Wainwright\CasinoDog\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use Wainwright\CasinoDog\CasinoDog;
use Wainwright\CasinoDog\Controllers\Game\SessionsHandler;
use Wainwright\CasinoDog\Controllers\Game\OperatorsController;
use Wainwright\CasinoDog\Traits\ApiResponseHelper;
use Wainwright\CasinoDog\Models\Gameslist;
use Illuminate\Support\Facades\Cache;
class APIController
{
   use ApiResponseHelper;

    public function providerslist_wainwright($providers, $count) {
        if($count < 2) {
            $games_count = Gameslist::where('provider', $providers[0]['slug'])->count();
            $providerslist = array(
                'id' => $providers[0]['slug'],
                'slug' => $providers[0]['slug'],
                'name' => ucfirst($providers[0]['name']),
                'parent' => NULL,
                'eligible_games' => $games_count,
                'icon' => 'ResponsiveIcon',
                'provider' => $providers[0]['provider'],
                'created_at' => now(),
                'updated_at' => now(),
            );
        } else {
        foreach($providers as $provider) {
            $games_count = Gameslist::where('provider', $provider['slug'])->count();
            $providerslist[] = array(
                'id' => $provider['slug'],
                'slug' => $provider['slug'],
                'name' => ucfirst($provider['slug']),
                'parent' => NULL,
                'eligible_games' => $games_count,
                'icon' => 'ResponsiveIcon',
                'provider' => $provider['slug'],
                'created_at' => now(),
                'updated_at' => now(),
            );
        }
        }


        return $providerslist;
    }

    public function game_descriptions() {
        $cache_length = 300; // 300 seconds = 5 minutes

        if($cache_length === 0) {
            $game_desc = file_get_contents(__DIR__.'../../game_descriptions.json');
        }
        $game_desc = Cache::remember('gameDescriptions', 300, function () {
            return file_get_contents(__DIR__.'/../../game_descriptions.json');
        });
        $g2 = json_decode($game_desc, true);

        return $g2;
    }

   public function gamesListEndpoint(string $layout, Request $request)
   {
        $gameslist = DB::table('wainwright_gameslist')->get();
        return response()->json($gameslist, 200);
   }


   public function providersListEndpoint(Request $request)
   {
    $cache_length = 60;
    $limit = 25;
    if($request->limit) {
        if(is_numeric($request->limit)) {
            if($request->limit > 0) {
                if($request->limit > 100) {
                    $limit = (int) 100;
                } else {
                $limit = (int) $request->limit;
                }
            }
        }
    }

    $providers = collect(Gameslist::providers());
    return collect($this->providerslist_wainwright($providers, $limit))->paginate($limit);
   }


    public function createSessionIframed(Request $request)
    {
        $validate = $this->createSessionValidation($request);
        if($validate->status() !== 200) {
            return $validate;
        }

        $data = [
            'game' => $request->game,
            'currency' => $request->currency,
            'player' => $request->player,
            'operator_key' => $request->operator_key,
            'mode' => $request->mode,
            'request_ip' => CasinoDog::requestIP($request),
        ];

        $session_create = SessionsHandler::createSession($data);
        if($session_create['status'] === 'success') {

            $data = [
                'session' => $session_create['message'],
                'ably' => [
                    'channel' => $session_create['message']['data']['token_internal'],
                    'key' => 'DnzkiQ.C6XmFg:IeY501QwXXAVDqIt6cOZCkjiXVbn0bD6ZJfi4Qsgzq8',
                ],
            ];

            return view('wainwright::iframed-view')->with('game_data', $data);
        } else {
            return $this->respondError($session_create);
        }
    }


    public function createSessionAndRedirectEndpoint(Request $request)
    {
        $validate = $this->createSessionValidation($request);
        if($validate->status() !== 200) {
            return $validate;
        }

        $data = [
            'game' => $request->game,
            'currency' => $request->currency,
            'player' => $request->player,
            'operator_key' => $request->operator_key,
            'mode' => $request->mode,
            'request_ip' => CasinoDog::requestIP($request),
        ];

        $session_create = SessionsHandler::createSession($data);
        if($session_create['status'] === 'success') {
            return redirect($session_create['message']['session_url']);
        } else {
            return $this->respondError($session_create);
        }
    }

   public function createSessionEndpoint(Request $request)
    {
        $validate = $this->createSessionValidation($request);
        if($validate->status() !== 200) {
            return $validate;
        }
        $data = [
            'game' => $request->game,
            'currency' => $request->currency,
            'player' => $request->player,
            'operator_key' => $request->operator_key,
            'mode' => $request->mode,
            'request_ip' => CasinoDog::requestIP($request),
        ];


        $session_create = SessionsHandler::createSession($data);
        if($session_create['status'] === 'success') {
            return response()->json($session_create, 200);
        } else {
            return $this->respondError($session_create);
        }
    }

    public function createSessionValidation(Request $request) {
        $validator = Validator::make($request->all(), [
            'game' => ['required', 'max:65', 'min:3'],
            'player' => ['required', 'min:3', 'max:100', 'regex:/^[^(\|\]`!%^&=};:?><’)]*$/'],
            'currency' => ['required', 'min:2', 'max:7'],
            'operator_key' => ['required', 'min:10', 'max:50'],
            'mode' => ['required', 'min:2', 'max:15'],
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            $errorReason = $validator->errors()->first();
            $prepareResponse = array('message' => $errorReason, 'request_ip' => CasinoDog::requestIP($request));
            return $this->respondError($prepareResponse);
        }

        $operator_verify = OperatorsController::verifyKey($request->operator_key, CasinoDog::requestIP($request));
        if($operator_verify === false) {
                $prepareResponse = array('message' => 'Operator key did not pass validation.', 'request_ip' => CasinoDog::requestIP($request));
                return $this->respondError($prepareResponse);
        }

        $operator_ping = OperatorsController::operatorPing($request->operator_key, CasinoDog::requestIP($request));
        if($operator_ping === false) {
            $prepareResponse = array('message' => 'Operator ping failed on callback.', 'request_ip' => CasinoDog::requestIP($request));
            return $this->respondError($prepareResponse);
        }

        if($request->mode !== 'real') {
            $prepareResponse = array('message' => 'Mode can only be \'demo\' or \'real\'.', 'request_ip' => CasinoDog::requestIP($request));
            return $this->respondError($prepareResponse);
        }
        return $this->respondOk();
    }
}
