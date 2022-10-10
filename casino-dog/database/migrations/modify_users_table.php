<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('mock_player_id', 100)->after('password')
                ->nullable()
                ->default(null);
            $table->string('mock_currency', 10)->after('mock_player_id')
                ->default('USD');
            $table->string('mock_balance', 50)->default('0')->after('mock_currency');
            $table->string('is_admin', 1)->default('0')->after('mock_balance');
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