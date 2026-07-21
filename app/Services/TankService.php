<?php

namespace App\Services;

class TankService
{
    public function levelToVolume(float $levelPercent, int $capacityLiters): int
    {
        return (int) round(($levelPercent / 100) * $capacityLiters);
    }
}
