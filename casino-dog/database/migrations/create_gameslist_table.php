<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wainwright_gameslist', function (Blueprint $table) {
            $table->id('id')->index();
            $table->string('gid', 100);
            $table->string('gid_extra', 200)->nullable();
            $table->string('batch', 100);
            $table->string('slug', 100);
            $table->string('name', 100);
            $table->string('provider', 100);
            $table->string('type', 100);
            $table->string('typeRating', 100);
            $table->string('popularity', 100);
            $table->integer('bonusbuy')->default(0);
            $table->integer('jackpot')->default(0);
            $table->integer('demoplay')->default(1);
            $table->string('demolink', 455)->nullable();
            $table->string('origin_demolink', 455);
            $table->string('image', 300)->nullable();
            $table->string('source', 50);
            $table->string('source_schema', 50);
            $table->string('method', 50);
            $table->json('realmoney', 1500);
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
        Schema::dropIfExists('wainwright_gameslist');
    }
};