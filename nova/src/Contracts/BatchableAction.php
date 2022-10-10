<?php

namespace Laravel\Nova\Contracts;

use Illuminate\Bus\PendingBatch;
use Laravel\Nova\Fields\ActionFields;

interface BatchableAction
{
    /**
     * Register `then`, `catch`, and `finally` callbacks on the pending batch.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Bus\PendingBatch  $batch
     * @return void
     */
    public function withBatch(ActionFields $fields, PendingBatch $batch);
}
