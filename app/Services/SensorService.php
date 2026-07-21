<?php

namespace App\Services;

class SensorService
{
    public function snapshot(array $state): array
    {
        return [
            'level_sensor' => $state['tank']['level_percent'],
            'pressure_sensor' => $state['tank']['pressure_bar'],
            'temperature_sensor' => $state['tank']['temperature_c'],
            'flow_meter' => $state['tank']['flow_rate_l_min'],
            'motor_current' => $state['pump']['motor_current_a'],
            'motor_temperature' => $state['pump']['motor_temperature_c'],
            'energy_consumption' => $state['pump']['energy_used_kwh'],
            'running_hours' => $state['pump']['running_hours'],
            'pump_efficiency' => $state['pump']['efficiency_percent'],
        ];
    }

    public function fluctuate(float $value, float $min, float $max, float $noise): float
    {
        $offset = (random_int(-100, 100) / 100) * $noise;

        return max($min, min($max, $value + $offset));
    }
}
