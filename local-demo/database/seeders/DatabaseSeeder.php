<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder { public function run(){ DB::table('demo_games')->updateOrInsert(['slug'=>'local-deterministic-slot'],['name'=>'Local Deterministic Slot','provider'=>'local-demo','enabled'=>1,'created_at'=>now(),'updated_at'=>now()]); } }
