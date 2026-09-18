<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
class DemoCasinoTest extends TestCase { use RefreshDatabase; public function test_end_to_end_demo_flow(){ $this->seed(); $r=$this->postJson('/api/demo/sessions',['game_id'=>'local-deterministic-slot','player_id'=>'trial-player','currency'=>'USD'])->assertCreated(); $token=$r->json('token'); $this->postJson("/api/demo/sessions/$token/events",['event'=>'init'])->assertOk()->assertJsonPath('balance',9900); $this->postJson("/api/demo/sessions/$token/events",['event'=>'spin','bet'=>100])->assertOk()->assertJsonPath('win',250)->assertJsonPath('balance',10050); $this->getJson("/api/demo/sessions/$token")->assertOk()->assertJsonPath('state','SESSION_STARTED'); } }
