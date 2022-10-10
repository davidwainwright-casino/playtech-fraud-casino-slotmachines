<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {

        Schema::table('users', function ($table) {
            $table->timestamp('api_created_at')->after('password')->nullable();
            $table->string('api_token', 100)->after('password')
                                ->unique()
                                ->nullable()
                                ->default(null);
            $table->string('active_currency', 100)->after('password')
                                ->nullable()
                                ->default('USD');
            $table->string('player_id', 100)->after('id')
                                ->nullable()
                                ->default(null);
            $table->string('operator_id', 100)->after('id')
                                ->nullable()
                                ->default(null);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
    
};