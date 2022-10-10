P<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wainwright_crawlerdata', function (Blueprint $table) {
            $table->id('id')->index();
            $table->string('url', 150);
            $table->string('state', 50);
            $table->string('state_message', 300);
            $table->string('extra_id', 50)->default(0);
            $table->string('type', 100)->default('[]');
            $table->json('result', 10000);
            $table->boolean('expired_bool')->default(0);
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
        Schema::dropIfExists('wainwright_crawlerdata');
    }
};
