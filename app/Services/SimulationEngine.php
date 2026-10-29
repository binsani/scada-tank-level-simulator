<?php

namespace App\Services;

use App\Support\DemoVisitorContext;
use App\Enums\OperatingMode;
use App\Enums\PumpState;
use App\Enums\SystemStatus;
use App\Enums\ValveState;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class SimulationEngine
{
    public function __construct(
        private readonly TankService $tankService,
        private readonly PumpService $pumpService,
        private readonly SensorService $sensorService,
        private readonly AlarmManager $alarmManager,
        private readonly EventLogger $eventLogger,
        private readonly TrendRecorder $trendRecorder,
        private readonly DemoVisitorContext $visitorContext,
    ) {
    }

    public function snapshot(): array
    {
        $state = $this->state();
        $this->advance($state);

        return $this->persist($state);
    }

    public function state(): array
    {
        return Cache::get($this->visitorContext->stateKey(), $this->defaultState());
    }

    public function startFilling(): array
    {
        $state = $this->state();
        $state['process'] = 'filling';
        $state['pump']['state'] = PumpState::Running->value;
        $state['valves']['inlet'] = ValveState::Opening->value;
        $state['valves']['outlet'] = ValveState::Closed->value;
        $state['system_status'] = SystemStatus::Simulation->value;

        $this->eventLogger->record('Pump Started', 'Process started filling mode.', ['action' => 'start']);

        return $this->persist($state);
    }

    public function stopProcess(): array
    {
        $state = $this->state();
        $state['process'] = 'stopped';
        $state['pump']['state'] = PumpState::Stopped->value;
        $state['valves']['inlet'] = ValveState::Closing->value;
        $state['valves']['outlet'] = ValveState::Closing->value;

        $this->eventLogger->record('Pump Stopped', 'Process stopped by operator.', ['action' => 'stop']);

        return $this->persist($state);
    }

    public function drainTank(): array
    {
        $state = $this->state();
        $state['process'] = 'draining';
        $state['pump']['state'] = PumpState::Running->value;
        $state['valves']['inlet'] = ValveState::Closed->value;
        $state['valves']['outlet'] = ValveState::Opening->value;

        $this->eventLogger->record('Tank Drain Started', 'Drain cycle initiated.', ['action' => 'drain']);

        return $this->persist($state);
    }

    public function emergencyStop(): array
    {
        $state = $this->state();
        $state['process'] = 'emergency_stop';
        $state['pump']['state'] = PumpState::Stopped->value;
        $state['valves']['inlet'] = ValveState::Closed->value;
        $state['valves']['outlet'] = ValveState::Closed->value;
        $state['system_status'] = SystemStatus::Offline->value;

        $this->eventLogger->record('Emergency Stop', 'Emergency stop activated.', ['action' => 'estop']);

        return $this->persist($state);
    }

    public function reset(): array
    {
        Cache::put($this->visitorContext->stateKey(), $this->defaultState(), now()->addHours(6));
        $this->eventLogger->record('Simulation Reset', 'Simulation state restored to defaults.', ['action' => 'reset']);

        return $this->state();
    }

    public function switchMode(string $mode): array
    {
        $state = $this->state();
        $state['operating_mode'] = in_array($mode, [OperatingMode::Auto->value, OperatingMode::Manual->value], true)
            ? $mode
            : OperatingMode::Manual->value;

        $this->eventLogger->record('Mode Changed', 'Operating mode switched.', ['mode' => $state['operating_mode']]);

        return $this->persist($state);
    }

    private function advance(array $state): void
    {
        $level = (float) $state['tank']['level_percent'];
        $process = $state['process'];
        $speed = (float) ($state['settings']['simulation_speed'] ?? 1.0);

        if ($process === 'filling') {
            $level += 1.25 * $speed;
        } elseif ($process === 'draining') {
            $level -= 1.5 * $speed;
        }

        $level = max(0.0, min(100.0, $level));
        $state['tank']['level_percent'] = round($level, 1);
        $state['tank']['volume_liters'] = (int) round(($level / 100) * (int) $state['tank']['capacity_liters']);
        $state['tank']['temperature_c'] = round($this->sensorService->fluctuate((float) $state['tank']['temperature_c'], 24, 31, 0.25), 1);
        $state['tank']['pressure_bar'] = round($this->sensorService->fluctuate((float) $state['tank']['pressure_bar'], 1.0, 3.8, 0.09), 2);
        $state['tank']['flow_rate_l_min'] = round($this->sensorService->fluctuate((float) $state['tank']['flow_rate_l_min'], 0, 55, 1.4), 1);
        $state['pump'] = $this->pumpService->advance($state['pump'], $process, $speed);
        $state['sensors'] = $this->sensorService->snapshot($state);
        $state['alarms'] = $this->alarmManager->evaluate($state);
        $state['timestamp'] = Carbon::now()->toIso8601String();

        $this->trendRecorder->record($state);

        $this->persist($state);
    }

    private function persist(array $state): array
    {
        Cache::put($this->visitorContext->stateKey(), $state, now()->addHours(6));

        return $state;
    }

    private function defaultState(): array
    {
        return [
            'process' => 'idle',
            'operating_mode' => OperatingMode::Auto->value,
            'system_status' => SystemStatus::Simulation->value,
            'timestamp' => now()->toIso8601String(),
            'settings' => config('scada'),
            'tank' => [
                'name' => 'Process Tank A-101',
                'capacity_liters' => config('scada.default_tank_capacity_liters'),
                'level_percent' => (float) config('scada.default_level_percent'),
                'volume_liters' => (int) round(config('scada.default_tank_capacity_liters') * config('scada.default_level_percent') / 100),
                'temperature_c' => 27.3,
                'pressure_bar' => 1.8,
                'flow_rate_l_min' => 18.4,
                'is_overflowing' => false,
            ],
            'pump' => [
                'state' => PumpState::Stopped->value,
                'running_hours' => 128.4,
                'motor_current_a' => 7.2,
                'motor_temperature_c' => 47.8,
                'energy_used_kwh' => 214.6,
                'efficiency_percent' => 86.1,
            ],
            'valves' => [
                'inlet' => ValveState::Closed->value,
                'outlet' => ValveState::Closed->value,
            ],
            'sensors' => [],
            'alarms' => [],
        ];
    }
}
