<?php

use App\Contracts\TankDataProvider;
use App\Http\Controllers\AlarmController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PumpController;
use App\Http\Controllers\TankController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// System Health Check Endpoint
Route::get('/health', function () {
    $dbOk = true;
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
    } catch (\Throwable $e) {
        $dbOk = false;
    }

    $cacheOk = true;
    try {
        \Illuminate\Support\Facades\Cache::put('scada.health.check', true, 10);
    } catch (\Throwable $e) {
        $cacheOk = false;
    }

    $status = ($dbOk && $cacheOk) ? 200 : 503;

    return response()->json([
        'status' => ($dbOk && $cacheOk) ? 'ok' : 'degraded',
        'database' => $dbOk ? 'connected' : 'error',
        'cache' => $cacheOk ? 'working' : 'error',
        'timestamp' => now()->toIso8601String(),
    ], $status);
});

// Telemetry & Data Read Endpoints (Rate Limited)
Route::middleware('throttle:120,1')->group(function () {
    Route::get('/tank', fn (TankDataProvider $provider) => response()->json($provider->snapshot()));
    Route::get('/pump', [PumpController::class, 'show']);
    Route::get('/sensors', fn (TankDataProvider $provider) => response()->json(data_get($provider->snapshot(), 'sensors', [])));
    Route::get('/alarms', [AlarmController::class, 'index']);
    Route::get('/events', fn (\App\Support\DemoHistoryScope $scope) => response()->json(['data' => $scope->apply(\App\Models\Event::query())->latest()->limit(100)->get()]));
});

// SCADA Process Control Endpoints (Strictly Rate Limited)
Route::middleware('throttle:30,1')->group(function () {
    Route::post('/start', fn (TankDataProvider $provider) => response()->json($provider->startFilling()));
    Route::post('/stop', fn (TankDataProvider $provider) => response()->json($provider->stopProcess()));
    Route::post('/reset', fn (TankDataProvider $provider) => response()->json($provider->reset()));
    Route::post('/manual', fn (TankDataProvider $provider) => response()->json($provider->switchToManual()));
    Route::post('/auto', fn (TankDataProvider $provider) => response()->json($provider->switchToAuto()));
});

