<?php

namespace App\Enums;

enum PumpState: string
{
    case Running = 'running';
    case Stopped = 'stopped';
    case Fault = 'fault';
    case Maintenance = 'maintenance';
}
