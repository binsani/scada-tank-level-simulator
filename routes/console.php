<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('scada:tick {--loop : Run continuously in a loop} {--interval=1 : Loop interval in seconds}', function (\App\Services\SimulationEngine $engine): void {
    $loop = (bool) $this->option('loop');
    $interval = max(1, (int) $this->option('interval'));

    if ($loop) {
        $this->info("Starting SCADA simulation loop (interval: {$interval}s)... Press Ctrl+C to stop.");
        while (true) {
            $snapshot = $engine->snapshot();
            $level = data_get($snapshot, 'tank.level_percent', 0);
            $process = data_get($snapshot, 'process', 'unknown');
            $this->line(sprintf('[%s] Tick executed - Process: %s | Tank Level: %.1f%%', now()->toTimeString(), $process, $level));
            sleep($interval);
        }
    } else {
        $snapshot = $engine->snapshot();
        $level = data_get($snapshot, 'tank.level_percent', 0);
        $this->info(sprintf('SCADA simulation tick executed. Level: %.1f%%', $level));
    }
})->purpose('Advance the tank simulation one step');

