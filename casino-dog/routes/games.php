<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Wainwright\CasinoDog\Controllers\Game\SessionsHandler;
use Wainwright\CasinoDog\Controllers\Game\Netent\NetentMain;


Route::domain('{parent_token}'.config('casino-dog.wildcard_session_domain.domain'))->group(function () {
    Route::middleware('api', 'throttle:5000,1')->group(function () {
    Route::get('/g', [SessionsHandler::class, 'entrySession']); // defaulted "entry" session location
    Route::get('/', [SessionsHandler::class, 'entryWildcardDomain']); // "entry" session location for your wildcard session domain setup, make sure to reverse proxy nginx towards /wildcard on your session domain
    Route::any('/EjsFrontWeb/fs', function (Request $request) {

        $url = "https://casino.nolimitcity.com/EjsFrontWeb/fs";
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $headers = array(
           "authority: casino.nolimitcity.com",
           "accept: application/json",
           "accept-language: en-ZA,en;q=0.9",
           "cache-control: no-cache",
           "content-type: application/x-www-form-urlencoded; charset=UTF-8",
           "origin: https://casino.nolimitcdn.com",
           "pragma: no-cache",
           "sec-ch-ua-mobile: ?0",
           "sec-fetch-dest: empty",
           "sec-fetch-mode: cors",
           "sec-fetch-site: cross-site",
           "user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $data = $request->getContent();
        //$data = "action=open_game&clientString=PARIMATCH&language=en&gameCodeString=PunkToilet%40desktop&jsonData=%7B%22cid%22%3A%22parimatch%22%2C%22productId%22%3A%22PunkToilet%22%2C%22lang%22%3A%22en%22%2C%22targetChannel%22%3A%22desktop%22%2C%22consumerId%22%3A%22nolimitcity%22%2C%22lobbyUrl%22%3A%22https%3A%2F%2Fpari-match.com%2Fen%2Fcasino%2Fslots%22%2C%22providerId%22%3A%22EVA_SLOTS_NOLIMITCITY%22%7D";
        
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $resp = curl_exec($curl);
        curl_close($curl);
        //$resp = str_replace('https://casino.nolimitcity.com/EjsGameWeb/gs', 'https://01-dev-casino.777.dog/EjsGameWeb/gs', $resp);
        //$resp = str_replace('https://casino.nolimitcity.com/EjsGameWeb/gs', 'https://01-dev-casino.777.dog/EjsGameWeb/gs', $resp);
        
        return $resp;
            
    });
    });
});
Route::domain(env('APP_HOSTNAME'))->group(function () {

    Route::middleware('api', 'throttle:5000,1')->group(function () {
        Route::get('/g', [SessionsHandler::class, 'entrySession'])->name('g'); // defaulted "entry" session location
    
        Route::get('/redirect_netent', [NetentMain::class, 'redirect_catch_content']); //used to hijack legitimate netent session to use our api 
        Route::get('/prelauncher_netent', [NetentMain::class, 'prelauncher_view']); //used to hijack legitimate netent session to use our api
        Route::get('/platipus/{game}/{game_code}/g', [SessionsHandler::class, 'entrySession']); //custom entry, being used in custom_entry_path() function on Platipus. You can toggle custom_entry_path usage in config/casino-dog.php
        Route::get('/Casino/IframedView', [SessionsHandler::class, 'entrySession']); //custom entry, being used in custom_entry_path() function on Playngo. You can toggle custom_entry_path usage in config/casino-dog.php
        Route::get('/casino/ContainerLauncher', [SessionsHandler::class, 'entrySession']); //custom entry, being used in custom_entry_path() function on Playngo. You can toggle custom_entry_path usage in config/casino-dog.php
        Route::get('/casino/IframedView', [SessionsHandler::class, 'entrySession']); //custom entry, being used in custom_entry_path() function on Playngo. You can toggle custom_entry_path usage in config/casino-dog.php

    
    });
});

## add to docs later self-note 
# These routes will end up in game controller set within casino-dog/config.php and function game_event(), for example: 
# https://yourappurl.com/api/games/pragmaticplay/{internal_token}/{slug}/{action} will end up at:
# PragmaticPlayMain::game_event(). 

# This makes easy for you to just "catch" all the game API traffic, after which you can edit requests before sending them to real game provider and change the responses from game provider when returning these to the player (for example with the player's balance, or if you want to skip a big win and re-spin the game result).

# All is made to be quite customizable, as every provider is different you should get used to creating/adapting to the provider instead of to package (as i've learnt myself). 
# Once you get the productional stage you should probably split up each provider seperately anyhow, though this can still remain a nice development/fuck-around package.


# You do not need to conform to the standard, also "{SLUG}" can be including slashes. Aslong the segments minimum is api/games/pragmaticplay/{internal_token}/{slug}/{action} but it will also catch:
# api/games/pragmaticplay/{internal_token}/whatever/whatever2/whatever3/{action}?query1=query2&query3=6

# In above example the $request->slug within your game controller will show as "whatever/whatever2/whatever3", also all query params are accessible as usual like $request->query1 etc.

# Fastest and easiest is to somehow create an API link when you modify the game HTML content to include the "internal_token" of the user's session, this way you can just select $request->internal_token and not need any additional data to select the player, operator & callback for the player balance.

Route::middleware('api', 'throttle:5000,1')->prefix('api/games')->group(function () {
Route::match(['get', 'post', 'head', 'patch', 'put', 'delete'] , '{provider}/{internal_token}/{slug}/{action}', function($provider, $internal_token, $slug, $action, Request $request) {
        $game_controller = config('casino-dog.games.'.$provider.'.controller');
        $game_controller_kernel = new $game_controller;
        return $game_controller_kernel->game_event($request);
    })->where('slug', '([A-Za-z0-9_.\-\/]+)');
});

Route::middleware('api', 'throttle:5000,1')->prefix('gs2c/')->group(function () { //pragmatic play promo game events, can be removed if not used
    Route::match(['get', 'post', 'head', 'patch', 'put', 'delete'] , 'announcements/{action}', function($action, Request $request) {
        $game_controller = config('casino-dog.games.pragmaticplay.controller');
        $game_controller_kernel = new $game_controller;
        return $game_controller_kernel->promo_event($request);
    })->where('slug', '([A-Za-z0-9_.\-\/]+)');

    Route::match(['get', 'post', 'head', 'patch', 'put', 'delete'] , 'promo/{action}', function($action, Request $request) {
        $game_controller = config('casino-dog.games.pragmaticplay.controller');
        $game_controller_kernel = new $game_controller;
        return $game_controller_kernel->promo_event($request);
    })->where('slug', '([A-Za-z0-9_.\-\/]+)');

});

Route::middleware('api', 'throttle:5000,1')->prefix('dynamic_asset/')->group(function ($provider) { 
# Used so you can use a generalized syntax for assets you wish to edit dynamically. Usually want to use this when provider uses .js or .json files with config variables instead of within HTML or url params. Try to keep loading files to absolute minimum, as it affects game performance but also because all gameproviders are jampacked with crap it will be very unsafe, really it is unsafe to load even single file like this and if needed a lot and/or for production you really want to save the game assets either statically (and sanitizing manually each game) or create seperate "launcher" instance where you only load html/js files. 

# These routes will end up in game controller set within casino-dog/config.php and function dynamic_asset(), for example: PragmaticPlayMain::dynamic_asset(). 

    Route::match(['get', 'post', 'head', 'patch', 'put', 'delete'] , '{provider}/{asset_name}', function($provider, $asset_name, Request $request) {
        $game_controller = config('casino-dog.games.'.$provider.'.controller');
        $game_controller_kernel = new $game_controller;
        return $game_controller_kernel->dynamic_asset($asset_name, $request);
    });
});

/*

Route::middleware('web', 'throttle:5000,1')->prefix('platipus')->group(function () {
    Route::match(['get', 'post', 'head', 'patch', 'put', 'delete'] , '{game}/{game_code}/{slug}', function($game, $game_code, $slug, Request $request) {
        $game_controller = config('casino-dog.games.platipus.controller');
        $game_controller_kernel = new $game_controller;
        header('Content-Type: application/x-javascript');
        echo $game_controller_kernel->dynamic_asset($game, $game_code, $slug, $request);
        //return $game_controller_kernel->dynamic_asset($game, $game_code, $slug, $request);
        })->where('slug', '([A-Za-z0-9_.\-\/]+)');
});


Route::middleware('api', 'throttle:5000,1')->prefix('api/c/platipus')->group(function () {
    Route::match(['get', 'post', 'head', 'patch', 'put', 'delete'] , '{internal_token}/onlinecasino/games/{game}/{action}', function($internal_token, $game, $action, Request $request) {
        $game_controller = config('casino-dog.games.platipus.controller');
        $game_controller_kernel = new $game_controller;
        return $game_controller_kernel->game_event($internal_token, $game, $action, $request);
    });
});

Route::middleware('api', 'throttle:5000,1')->group(function () {
    Route::match(['get', 'post', 'head', 'patch', 'put', 'delete'] , 'wurfl.js', function(Request $request) {
        $game_controller = config('casino-dog.games.pragmaticplay.controller');
        $game_controller_kernel = new $game_controller;
        return $game_controller_kernel->dynamic_asset('wurfl.js', $request);
    });
});

Route::match(['get', 'post', 'head', 'patch', 'put', 'delete'] , 'play_isb', function(Request $request) {
    $game_controller = config('casino-dog.games.isoftbet.controller');
    $game_controller_kernel = new $game_controller;
    return $game_controller_kernel->game_event($request);
})->where('slug', '([A-Za-z0-9_.\-\/]+)');
*/

