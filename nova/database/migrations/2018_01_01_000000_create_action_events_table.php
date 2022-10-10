<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;
use Laravel\Nova\Util;

class CreateActionEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_events', function (Blueprint $table) {
            $table->id();
            $table->char('batch_id', 36);
            $table->foreignIdFor(Util::userModel(), 'user_id')->index();
            $table->string('name');
            $table->morphs('actionable');
            $table->morphs('target');
            $table->string('model_type');

            if (Builder::$defaultMorphKeyType === 'uuid') {
                $table->uuid('model_id')->nullable();
            } else {
                $table->unsignedBigInteger('model_id')->nullable();
            }

            $table->text('fields');
            $table->string('status', 25)->default('running');
            $table->text('exception');
            $table->timestamps();

            $table->index(['batch_id', 'model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action_events');
    }
}
