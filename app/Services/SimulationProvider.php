<?php

namespace App\Services;

use App\Contracts\TankDataProvider;
use App\Models\Alarm;
use App\Models\Event;
use App\Models\Pump;
use App\Models\Sensor;
use App\Models\Setting;
use App\Models\Tank;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class SimulationProvider implements TankDataProvider
{
    public function __construct(
        private readonly SimulationEngine $engine,
        private readonly TankService $tankService,
        private readonly PumpService $pumpService,
        private readonly SensorService $sensorService,
        private readonly AlarmManager $alarmManager,
        private readonly EventLogger $eventLogger,
        private readonly TrendRecorder $trendRecorder,
    ) {
    }

    public function snapshot(): array
    {
        return $this->engine->snapshot();
    }

    public function state(): array
    {
        return $this->engine->state();
    }

    public function startFilling(): array
    {
        return $this->engine->startFilling();
    }

    public function stopProcess(): array
    {
        return $this->engine->stopProcess();
    }

    public function drainTank(): array
    {
        return $this->engine->drainTank();
    }

    public function emergencyStop(): array
    {
        return $this->engine->emergencyStop();
    }

    public function reset(): array
    {
        return $this->engine->reset();
    }

    public function switchToAuto(): array
    {
        return $this->engine->switchMode('auto');
    }

    public function switchToManual(): array
    {
        return $this->engine->switchMode('manual');
    }
}
