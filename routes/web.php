<?php

use App\Http\Controllers\AlarmController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PumpController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TankController;
use Illuminate\Support\Facades\Route;

Route::get('/up', fn () => response('OK', 200))->name('health.up');

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.view');
Route::get('/tanks/current', [TankController::class, 'show'])->name('tank.show');
Route::get('/pump/current', [PumpController::class, 'show'])->name('pump.show');
Route::get('/alarms', [AlarmController::class, 'index'])->name('alarms.index');
Route::post('/alarms/{alarm}/acknowledge', [AlarmController::class, 'acknowledge'])->name('alarms.acknowledge');
Route::post('/alarms/{alarm}/clear', [AlarmController::class, 'clear'])->name('alarms.clear');
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

