<?php

namespace App\Services;

class AlarmManager
{
    public function evaluate(array $state): array
    {
        $alarms = [];
        $level = (float) $state['tank']['level_percent'];

        if ($level >= 95) {
            $alarms[] = $this->alarm('critical', 'HIGH HIGH LEVEL ALARM', 'Tank', 'active');
        } elseif ($level >= 80) {
            $alarms[] = $this->alarm('high', 'High Level Warning', 'Tank', 'active');
        }

        if ($level <= 5) {
            $alarms[] = $this->alarm('critical', 'LOW LOW LEVEL ALARM', 'Tank', 'active');
        } elseif ($level <= 15) {
            $alarms[] = $this->alarm('high', 'Low Level Warning', 'Tank', 'active');
        }

        return $alarms;
    }

    private function alarm(string $priority, string $message, string $equipment, string $status): array
    {
        return compact('priority', 'message', 'equipment', 'status');
    }
}
