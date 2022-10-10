<?php
namespace Wainwright\CasinoDog\Controllers\Game\Nolimitcity;

use Illuminate\Support\Facades\Http;
use Wainwright\CasinoDog\Facades\ProxyHelperFacade;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Illuminate\Support\Facades\Cache;

class NolimitcityGame extends NolimitcityMain
{
    use GameKernelTrait;

    /* Nolimitcity is example of using Pusher and is unfinished, instead you should make a websocket tunnel probably if wanting to use productional as simply being too slow, also the integrity is hard to verify
       while if you tunnel the websocket you can be a 100% sure the game results are legitimate coming from the source and not been modified with.
    */
    public function game_event($request)
    {
        $json = $request->events;
        $event = $json[0]['event'];
        $channel = $json[0]['channel'];
        $data = json_decode($json[0]['data'], true);
        $internal_token = str_replace('private-', '', $json[0]['channel']);
        if($event === 'client-nolimitcity-spin') {
            $bet_amount = (int) $data['data']['playedBetValue'] * 100;
            $win_amount = (int) $data['data']['totalSpinWinnings'] * 100;
            $process_and_get_balance = $this->process_game($internal_token, ($bet_amount ?? 0), ($win_amount ?? 0), $data);
            return \Wainwright\CasinoDog\Events\SendPusherBroadcast::dispatch($channel, 'balance-event', floatval($process_and_get_balance / 100));
        }
        if($event === 'client-nolimitcity-updatebalance') {
            $get_balance = $this->get_balance($internal_token);
            return \Wainwright\CasinoDog\Events\SendPusherBroadcast::dispatch($channel, 'balance-event', floatval($get_balance / 100));
        }
    }
}


/*

    0 => 
    array (
      'channel' => 'private-97750ec9-0db6-4aef-9dca-5bc40a4bc54a',
      'data' => '{"name":"game","data":{"reels":[["L1","M3","M5","M1","L3","M4"],["M1","L1","L3","M4","M5","M1"],["M3","M5","M1","L1","L3","M4"],["M4","L2","M3","M5","L1","M2"],["M1","M3","M1","S","M5","M1"]],"evaluatedArea":[["X","X","M5","M1","X","X"],["X","X","L3","M4","X","X"],["X","X","M1","L1","X","X"],["X","X","M3","M5","X","X"],["X","X","X","X","X","X"]],"openArea":[[false,false,true,true,false,false],[false,false,true,true,false,false],[false,false,true,true,false,false],[false,false,true,true,false,false],[false,false,false,false,false,false]],"betWayWins":[],"totalBetWayWinnings":0,"totalSpinWinnings":0,"accumulatedRoundWin":0,"playedBetValue":1,"reelsNextSpin":"BASE_REELSET","mode":"NORMAL","nextMode":"NORMAL","freespinTriggeredThisSpin":false,"numberOfFreespinsPlayed":0,"freespinsLeft":0,"addedNumberOfFreespinsThisSpin":0,"wasFeatureBuy":false,"totalNumberWaysWon":0,"brokeBank":false,"enhancedBet":false,"roach":{"multipliers":[[0,1,1,1,1,0],[1,1,1,1,1,1],[1,1,1,1,1,1],[1,1,1,1,1,1],[0,1,1,1,1,0]],"path":[],"path2":[],"path3":[],"path4":[],"roachLevels":[0,0,0,0],"allLevels":[1,2,3,10],"hives":[],"xtraRoach":false,"xtraRoachThisSpin":false,"eggs2":false,"upgradeLevelOfClonedRoach":0,"pathIndexForUpgrade":[[0,0],[0,0],[0,0],[0,0]]},"symbolSplits":{"bookOfWaysVertical":[[0,1,1,1,1,0],[1,1,1,1,1,1],[1,1,1,1,1,1],[1,1,1,1,1,1],[0,1,1,1,1,0]],"bookOfWaysHorizontal":[[0,1,1,1,1,0],[1,1,1,1,1,1],[1,1,1,1,1,1],[1,1,1,1,1,1],[0,1,1,1,1,0]],"roachWays":[[0,1,1,1,1,0],[1,1,1,1,1,1],[1,1,1,1,1,1],[1,1,1,1,1,1],[0,1,1,1,1,0]],"bookOfWays":[[0,1,1,1,1,0],[1,1,1,1,1,1],[1,1,1,1,1,1],[1,1,1,1,1,1],[0,1,1,1,1,0]]},"alteredPath":false}}',
      'event' => 'client-nolimitcity-spin',
      'name' => 'client_event',
      'socket_id' => '137159.16050444',
    ),
*/