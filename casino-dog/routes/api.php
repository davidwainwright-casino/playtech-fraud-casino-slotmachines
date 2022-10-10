<?php
use Illuminate\Support\Facades\Route;
use Wainwright\CasinoDog\Controllers\APIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Route::middleware('api', 'throttle:5000,1')->prefix('api')->group(function () {
        Route::get('/createSession', [APIController::class, 'createSessionEndpoint']);
        Route::get('/createSessionAndRedirect', [APIController::class, 'createSessionAndRedirectEndpoint']);
        Route::get('/createSessionIframed', [APIController::class, 'createSessionIframed']);
      	Route::get('/gameslist/{layout}', [APIController::class, 'gamesListEndpoint']);

        
Route::any('/pusher_auth', function (Request $request) {
    $data =  [
        'id' => '1234567'
    ];
    $encoded = json_encode($data);
    $hash = hash_hmac('sha256', $request->socket_id.':'.$request->channel_name, env('PUSHER_APP_SECRET'));
    //$hash = hash_hmac('sha256', $request->socket_id.'::user::'.$encoded, env('PUSHER_APP_SECRET'));
    return [
        'auth' => env('PUSHER_APP_KEY').':'.$hash,
        'user_data' => $data
    ];
  });
        
  Route::any('pusher_webhooks', function (Request $request) {
      $json = $request->events;
      $event = $json[0]['event'];
      $channel = $json[0]['channel'];
      $provider_explode = explode('-', $event);
      if(isset($provider_explode[1])) {
        $provider = $provider_explode[1];
        $game_controller = config('casino-dog.games.'.$provider.'.controller');
        $game_controller_kernel = new $game_controller;
        return $game_controller_kernel->game_event($request);
      }
});
});
