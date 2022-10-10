P<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wainwright_bgaming_bonusgames', function (Blueprint $table) {
            $table->id('id')->index();
            $table->string('bonusgame_token', 100);
            $table->string('player_id', 100);
            $table->string('game_id', 100);
            $table->json('game_event', 5000);
            $table->json('init_event', 5000);
            $table->boolean('replayed', 15);
            $table->boolean('active', 15);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wainwright_bgaming_bonusgames');
    }
};