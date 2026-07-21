<?php

namespace Database\Seeders;

use App\Models\Pump;
use App\Models\Setting;
use App\Models\Tank;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'operator@scada.local',
        ], [
            'name' => 'Simulation Operator',
            'password' => Hash::make('password'),
            'role' => 'operator',
        ]);

        $tank = Tank::query()->updateOrCreate([
            'name' => 'Process Tank A-101',
        ], [
            'capacity_liters' => 1000,
            'level_percent' => 35,
            'temperature_c' => 27.3,
            'pressure_bar' => 1.8,
            'flow_rate_l_min' => 18.4,
            'operating_mode' => 'auto',
            'system_status' => 'simulation',
            'is_overflowing' => false,
        ]);

        Pump::query()->updateOrCreate([
            'tank_id' => $tank->id,
        ], [
            'name' => 'Main Pump',
            'state' => 'stopped',
            'running_hours' => 128.4,
            'motor_current_a' => 7.2,
            'motor_temperature_c' => 47.8,
            'energy_used_kwh' => 214.6,
            'efficiency_percent' => 86.1,
        ]);

        foreach ([
            'tank_capacity' => 1000,
            'simulation_speed' => 1.0,
            'alarm_thresholds' => config('scada.alarm_thresholds'),
            'sensor_noise' => config('scada.sensor_noise'),
            'units' => 'metric',
        ] as $key => $value) {
            Setting::query()->updateOrCreate([
                'key' => $key,
            ], [
                'group' => 'simulation',
                'value' => $value,
            ]);
        }
    }
}
