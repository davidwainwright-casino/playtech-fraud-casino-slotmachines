<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
class DemoCasinoController extends Controller
{
 public function health(){ Redis::ping(); return response()->json(['ok'=>true,'database'=>DB::connection()->getPdo()!==null,'redis'=>true]); }
 public function catalog(){ return response()->json(['architecture'=>'local demo adapter','games'=>DB::table('demo_games')->where('enabled',1)->get()]); }
 public function createSession(Request $r){ $data=$r->validate(['game_id'=>'required|string','player_id'=>'required|string','currency'=>'nullable|string']); $game=DB::table('demo_games')->where('slug',$data['game_id'])->where('enabled',1)->first(); abort_unless($game,404,'Game unavailable'); $token=(string) Str::uuid(); DB::table('demo_sessions')->insert(['token'=>$token,'game_id'=>$game->slug,'player_id'=>$data['player_id'],'currency'=>$data['currency']??'USD','balance'=>10000,'state'=>'SESSION_INIT','created_at'=>now(),'updated_at'=>now()]); return response()->json(['token'=>$token,'game'=>$game,'balance'=>10000],201); }
 public function session(string $token){ $s=DB::table('demo_sessions')->where('token',$token)->firstOrFail(); return response()->json($s); }
 public function event(Request $r,string $token){ $data=$r->validate(['event'=>'required|in:init,spin','bet'=>'nullable|integer|min:0']); return DB::transaction(function() use($data,$token){ $s=DB::table('demo_sessions')->where('token',$token)->lockForUpdate()->firstOrFail(); $bet=(int)($data['bet']??100); abort_if($bet>$s->balance,422,'Insufficient demo balance'); $win=$data['event']==='spin'?250:0; $new=$s->balance-$bet+$win; $id=(string) Str::uuid(); DB::table('demo_sessions')->where('token',$token)->update(['balance'=>$new,'state'=>'SESSION_STARTED','updated_at'=>now()]); DB::table('demo_transactions')->insert(['transaction_id'=>$id,'session_token'=>$token,'event'=>$data['event'],'bet'=>$bet,'win'=>$win,'balance_after'=>$new,'created_at'=>now(),'updated_at'=>now()]); return response()->json(['transaction_id'=>$id,'event'=>$data['event'],'bet'=>$bet,'win'=>$win,'balance'=>$new]); }); }
}
