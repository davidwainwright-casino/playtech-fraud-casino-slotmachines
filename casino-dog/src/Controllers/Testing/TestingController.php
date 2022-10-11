<?php
namespace Wainwright\CasinoDog\Controllers\Testing;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Wainwright\CasinoDog\Controllers\Game\GameKernel;

use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use DB;
use Wainwright\CasinoDog\CasinoDog;
use Wainwright\CasinoDog\Facades\ProxyHelperFacade;

class TestingController
{
    use GameKernelTrait;
    public function __construct() {

        if(env('APP_ENV') !== 'local') {
            abort(400, 'Only available in APP_ENV=local');
        }
    }
    public function handle($function = NULL, Request $request) {
        if($function !== 'pulse_super_diamond_wild_game.html') {
        return $this->$function($request);
        }
    }

    public static function nl(Request $request) {

        $url = "https://rarenew-dk4.pragmaticplay.net/gs2c/playGame.do?key=token%3Dea30fcc7-c0d1-49ae-ba12-5116f646515a%26symbol%3Dvs20olympgate%26platform%3DWEB%26language%3Den%26currency%3DUSD%26cashierUrl%3Dhttps%3A%2F%2Fstake.com%2Fdeposit%26lobbyUrl%3Dhttps%3A%2F%2Fstake.com%2Fcasino%2Fhome&stylename=rare_stake";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $headers = array(
           "authority: rarenew-dk4.pragmaticplay.net",
           "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
           "accept-language: en-ZA,en;q=0.9",
           "cache-control: no-cache",
           "pragma: no-cache",
           "upgrade-insecure-requests: 1",
           "user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.101 Mobile Safari/537.36",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $resp = curl_exec($curl);
        curl_close($curl);
        return Http::get($url);

    }



    public function viewer()
    {
        return Http::patch('https://rarenew-dk4.pragmaticplay.net/ReplayService');

    }
    
    public static function send(Request $request) {
//return \Wainwright\CasinoDog\Events\TestBroadcast::dispatch('5000');
        $url = "https://api-prod.infingame.com/ps-launch/softswiss/bets/prod?gameName=pls_luxor_gold_hold_and_win&key=TEST1000&country=RUS&demo=true&shell=request&language=en&segment=desktop";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        echo "<iframe src='$url'></iframe>";
        return $resp;
}


    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    public function test_get_config(Request $request)
    {
        $game_controller = config('casino-dog.games');

        if (file_exists(config_path('../.wainwright/casino-dog/config/casino-dog.php'))) {
            $this->replaceInFile($game_controller, $game_controller.$game_controller, config_path('casino-dog.php'));
        }

        return config_path('casino-dog.games');
    }


}
