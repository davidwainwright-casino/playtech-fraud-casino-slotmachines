<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wainwright_games_thumbnails', function (Blueprint $table) {
            $table->id('id')->index();
            $table->string('img_gid', 100);
            $table->string('img_url', 300);
            $table->string('img_ext', 100);
            $table->string('ownedBy', 100);
            $table->integer('active')->default(1);
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
        Schema::dropIfExists('wainwright_games_thumbnails');
    }
};