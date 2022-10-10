<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wainwright_job_gameimporter', function (Blueprint $table) {
            $table->id('id')->index();
            $table->string('link', 200);
            $table->string('filter_key', 100)->nullable();
            $table->string('filter_value', 150)->nullable();
            $table->string('schema', 35)->default('softswiss');
            $table->string('state', 35)->default('JOB_QUEUED');
            $table->string('state_message', 3500)->default('N/A');
            $table->boolean('proxy', 15)->default(true);
            $table->string('imported_games', 25)->default('0');
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
        Schema::dropIfExists('wainwright_job_gameimporter');
    }
};