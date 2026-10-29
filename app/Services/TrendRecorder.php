<?php

namespace App\Services;

use App\Support\DemoVisitorContext;

use App\Models\TrendData;

class TrendRecorder
{
    public function __construct(private readonly DemoVisitorContext $visitorContext)
    {
    }

    public function record(array $state): void
    {
        try {
            TrendData::create([
                'demo_visitor_key' => $this->visitorContext->historyKey(),
                'tank_level' => $state['tank']['level_percent'] ?? 0,
                'pressure' => $state['tank']['pressure_bar'] ?? 0,
                'temperature' => $state['tank']['temperature_c'] ?? 0,
                'flow_rate' => $state['tank']['flow_rate_l_min'] ?? 0,
                'motor_current' => $state['pump']['motor_current_a'] ?? 0,
                'energy_consumption' => $state['pump']['energy_used_kwh'] ?? 0,
                'recorded_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('TrendRecorder failed: '.$e->getMessage());
        }
    }
}
