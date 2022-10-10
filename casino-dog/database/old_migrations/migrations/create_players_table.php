<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('respins_players', function (Blueprint $table) {
            $table->bigIncrements('id')->primaryKey;
            $table->foreignUuid('player_id', 150)->unique();
            $table->string('player_operator_id', 100)->nullable();
            $table->string('operator_key', 100)->nullable();
            $table->string('nickname', 155)->nullable();
            $table->string('currency', 155)->nullable();
            $table->boolean('active', 10);
            $table->json('data', 1500);
            $table->unsignedBigInteger('ownedBy');
            $table->foreign('ownedBy')->references('id')->on('users');
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
        Schema::dropIfExists('respins_players');
    }
};