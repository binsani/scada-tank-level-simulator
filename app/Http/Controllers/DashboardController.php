<?php

namespace App\Http\Controllers;

use App\Contracts\TankDataProvider;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly TankDataProvider $tankDataProvider)
    {
    }

    public function index(): View
    {
        try {
            $snapshot = $this->tankDataProvider->snapshot();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Dashboard snapshot failed: '.$e->getMessage());
            $snapshot = $this->getDefaultSnapshot();
        }

        return view('dashboard', [
            'snapshot' => $snapshot,
            'state' => $snapshot,
            'pageTitle' => 'Tank Level SCADA Simulator',
        ]);
    }

    private function getDefaultSnapshot(): array
    {
        return [
            'process' => 'idle',
            'operating_mode' => 'auto',
            'system_status' => 'simulation',
            'tank' => [
                'name' => 'Process Tank A-101',
                'capacity_liters' => 1000,
                'level_percent' => 35.0,
                'volume_liters' => 350,
                'temperature_c' => 27.3,
                'pressure_bar' => 1.8,
                'flow_rate_l_min' => 18.4,
                'is_overflowing' => false,
            ],
            'pump' => [
                'state' => 'stopped',
                'running_hours' => 128.4,
                'motor_current_a' => 7.2,
                'motor_temperature_c' => 47.8,
                'energy_used_kwh' => 214.6,
                'efficiency_percent' => 86.1,
            ],
            'valves' => [
                'inlet' => 'closed',
                'outlet' => 'closed',
            ],
            'sensors' => [],
            'alarms' => [],
        ];
    }
}
