P<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wainwright_bridge_sessions', function (Blueprint $table) {
            $table->id('id')->index();
            $table->string('bridge_session_token', 100);
            $table->string('entry_session_token', 100);
            $table->string('parent_session', 100);
            $table->unsignedBigInteger('active', 5);
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
        Schema::dropIfExists('wainwright_bridge_sessions');
    }
};