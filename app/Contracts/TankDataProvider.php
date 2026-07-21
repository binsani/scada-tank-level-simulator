<?php

namespace App\Contracts;

interface TankDataProvider
{
    public function snapshot(): array;

    public function state(): array;

    public function startFilling(): array;

    public function stopProcess(): array;

    public function drainTank(): array;

    public function emergencyStop(): array;

    public function reset(): array;

    public function switchToAuto(): array;

    public function switchToManual(): array;
}
