<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wainwright_metadata', function (Blueprint $table) {
            $table->id('id')->index();
            $table->string('key', 100);
            $table->string('type', 100)->nullable();
            $table->string('value', 100)->nullable();
            $table->string('extended_key', 100)->nullable();;
            $table->json('object_data', 1500);
            $table->boolean('active');
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
        Schema::dropIfExists('wainwright_metadata');
    }
};