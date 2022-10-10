<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('respins_user_balance', function (Blueprint $table) {
            $table->id('id')->index();
            $table->string('uid', 100);
            $table->string('key', 100);
            $table->string('value', 100);
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
        Schema::dropIfExists('respins_user_balances');
    }
    
};


