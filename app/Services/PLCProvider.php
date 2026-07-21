<?php

namespace App\Services;

use App\Contracts\TankDataProvider;

class PLCProvider implements TankDataProvider
{
    public function __construct(private readonly array $transportConfig = [])
    {
    }

    public function snapshot(): array
    {
        return $this->placeholder('snapshot');
    }

    public function state(): array
    {
        return $this->placeholder('state');
    }

    public function startFilling(): array
    {
        return $this->placeholder('startFilling');
    }

    public function stopProcess(): array
    {
        return $this->placeholder('stopProcess');
    }

    public function drainTank(): array
    {
        return $this->placeholder('drainTank');
    }

    public function emergencyStop(): array
    {
        return $this->placeholder('emergencyStop');
    }

    public function reset(): array
    {
        return $this->placeholder('reset');
    }

    public function switchToAuto(): array
    {
        return $this->placeholder('switchToAuto');
    }

    public function switchToManual(): array
    {
        return $this->placeholder('switchToManual');
    }

    private function placeholder(string $action): array
    {
        return [
            'provider' => 'plc',
            'action' => $action,
            'status' => 'unconfigured',
            'message' => 'PLC provider placeholder. Configure Modbus TCP, OPC UA, or MQTT transport before use.',
        ];
    }
}
