<?php
namespace Wainwright\CasinoDog\Controllers\Game\Netent;

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

class NetentGame extends NetentMain
{
    use GameKernelTrait;


    public function bridged($request) {
        $internal_token = $request->internal_token;
        $select_session = $this->get_internal_session($internal_token)['data'];
        $url = $_SERVER['REQUEST_URI'];
        $exploded_url = explode(';jsession', $url);
        if(isset($exploded_url[1])) {
        $callback_url = 'https://netentff-game.casinomodule.com/servlet/CasinoGameServlet;jsession'.$exploded_url[1];
        $http = Http::get($callback_url);
            if($request->action === 'init') {
                $data_origin = $this->parse_query($http);
                $get_balance = $this->get_balance($internal_token);
                $credit_current = $this->in_between("\&credit=", "\&", $http);
                if($credit_current) {
                    $bridge_balance = (int) Cache::set($internal_token.':netentHiddenBalance',  (int) $data_origin['credit']);
                    $http = str_replace('credit='.$credit_current, 'credit='.$get_balance, $http);
                }
                $http = str_replace('playforfun=true', 'playforfun=false', $http);
                $http = str_replace('g4mode=false', 'g4mode=true', $http);
                return $http;
            }

            $data_origin = $this->parse_query($http);
            $data_origin['playforfun'] = false;
            $data_origin['g4mode'] = true;

            if(isset($data_origin['credit'])) {
                $bridge_balance = (int) Cache::get($internal_token.':netentHiddenBalance');
                if(!$bridge_balance) {
                    $bridge_balance = (int) Cache::set($internal_token.':netentHiddenBalance',  (int) $data_origin['credit']);
                }
                $current_balance = (int) $data_origin['credit'];
                if($bridge_balance !== $current_balance) {
                    if($bridge_balance > $current_balance) {
                        $winAmount = 0;
                        $betAmount = $bridge_balance - $current_balance;
                    } else {
                        $betAmount = 0;
                        $winAmount = $current_balance - $bridge_balance;
                    }
                Cache::set($internal_token.':netentHiddenBalance',  (int) $current_balance);
                $process_and_get_balance = $this->process_game($internal_token, ($betAmount ?? 0), ($winAmount ?? 0), $data_origin);
                $data_origin['credit'] = (int) $process_and_get_balance;
                } else {
                    Cache::set($internal_token.':netentHiddenBalance',  (int) $current_balance);
                    $get_balance = $this->get_balance($internal_token);
                    $data_origin['credit'] = (int) $get_balance;
                }
            }

            $build = $this->build_query($data_origin);
            $final = str_replace('_', '.', $build);
	    return $final;

        } else {
            $callback_url = 'https://netentff-game.casinomodule.com/mobile-game-launcher/version';
            $send_request = $this->curl_request($callback_url, $request);
            return $send_request;
        }
    }

    public function game_event($request)
    {
	$returns = $this->bridged($request);
	//$final = str_replace('   ', '', $this->bridged($request));
	return $returns;
    }


    public function curl_request($url, $request)
    {
        $resp = ProxyHelperFacade::CreateProxy($request)->toUrl($url);

        return $resp;
    }
}
