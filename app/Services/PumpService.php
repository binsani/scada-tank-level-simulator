<?php

namespace App\Services;

use App\Enums\PumpState;

class PumpService
{
    public function advance(array $pump, string $process, float $speed): array
    {
        $running = $pump['state'] === PumpState::Running->value;

        if ($running) {
            $pump['running_hours'] = round(((float) $pump['running_hours']) + (1 / 3600) * $speed, 3);
            $pump['motor_current_a'] = round($this->clamp((float) $pump['motor_current_a'] + $this->variance(0.3), 5, 15), 1);
            $pump['motor_temperature_c'] = round($this->clamp((float) $pump['motor_temperature_c'] + $this->variance(0.6), 35, 80), 1);
            $pump['energy_used_kwh'] = round(((float) $pump['energy_used_kwh']) + 0.015 * $speed, 3);
            $pump['efficiency_percent'] = round($this->clamp((float) $pump['efficiency_percent'] + $this->variance(0.7), 60, 98), 1);
        } else {
            $pump['motor_current_a'] = round($this->clamp((float) $pump['motor_current_a'] - 0.25, 0, 15), 1);
            $pump['motor_temperature_c'] = round($this->clamp((float) $pump['motor_temperature_c'] - 0.4, 20, 80), 1);
        }

        return $pump;
    }

    private function variance(float $range): float
    {
        return ((random_int(-100, 100) / 100) * $range);
    }

    private function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }
}
