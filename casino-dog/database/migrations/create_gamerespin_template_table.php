<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wainwright_gamerespin_template', function (Blueprint $table) {
            $table->id('id')->index();
            $table->string('gid', 200);
            $table->string('game_data', 2500);
            $table->string('game_type', 200);
            $table->boolean('enabled');
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
        Schema::dropIfExists('wainwright_gamerespin_template');
    }
};