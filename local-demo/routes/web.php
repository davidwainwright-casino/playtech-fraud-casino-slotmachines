<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoCasinoController;
Route::get('/', [DemoCasinoController::class, 'catalog']);
Route::get('/play', fn () => response()->file(public_path('demo.html')));
Route::get('/health', [DemoCasinoController::class, 'health']);
Route::post('/api/demo/sessions', [DemoCasinoController::class, 'createSession']);
Route::post('/api/demo/sessions/{token}/events', [DemoCasinoController::class, 'event']);
Route::get('/api/demo/sessions/{token}', [DemoCasinoController::class, 'session']);
