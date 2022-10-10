<?php

use Illuminate\Support\Facades\Route;
use Wainwright\CasinoDog\Controllers\Testing\TestingController;

Route::middleware('web', 'throttle:2000,1')->group(function () {
Route::get('/testing/{function}', [TestingController::class, 'handle']);

});