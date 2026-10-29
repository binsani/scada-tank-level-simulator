<?php

namespace App\Providers;

use App\Contracts\TankDataProvider;
use App\Support\DemoVisitorContext;
use App\Services\AlarmManager;
use App\Services\EventLogger;
use App\Services\PumpService;
use App\Services\SensorService;
use App\Services\SimulationEngine;
use App\Services\SimulationProvider;
use App\Services\TankService;
use App\Services\TrendRecorder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TankService::class);
        $this->app->singleton(PumpService::class);
        $this->app->singleton(SensorService::class);
        $this->app->singleton(AlarmManager::class);
        $this->app->singleton(EventLogger::class);
        $this->app->singleton(TrendRecorder::class);

        // Request-scoped so long-lived workers cannot retain another visitor's context.
        $this->app->scoped(DemoVisitorContext::class, fn ($app) => new DemoVisitorContext($app->make('request')));
        $this->app->scoped(SimulationEngine::class, function ($app) {
            return new SimulationEngine(
                $app->make(TankService::class),
                $app->make(PumpService::class),
                $app->make(SensorService::class),
                $app->make(AlarmManager::class),
                $app->make(EventLogger::class),
                $app->make(TrendRecorder::class),
                $app->make(DemoVisitorContext::class),
            );
        });

        $this->app->scoped(TankDataProvider::class, function ($app) {
            return new SimulationProvider(
                $app->make(SimulationEngine::class),
                $app->make(TankService::class),
                $app->make(PumpService::class),
                $app->make(SensorService::class),
                $app->make(AlarmManager::class),
                $app->make(EventLogger::class),
                $app->make(TrendRecorder::class),
            );
        });
    }

    public function boot(): void
    {
    }
}
