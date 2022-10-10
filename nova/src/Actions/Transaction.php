<?php

namespace Laravel\Nova\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class Transaction
{
    /**
     * Perform the given callbacks within a batch transaction.
     *
     * @param  callable(string):mixed  $callback
     * @param  (callable(string):void)|null  $finished
     * @return mixed
     *
     * @throws \Throwable
     */
    public static function run($callback, $finished = null)
    {
        try {
            DB::beginTransaction();

            $actionBatchId = (string) Str::orderedUuid();

            return tap($callback($actionBatchId), function ($response) use ($finished, $actionBatchId) {
                if ($finished) {
                    $finished($actionBatchId);
                }

                DB::commit();
            });
        } catch (Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
