<?php

namespace App\Enums;

enum SystemStatus: string
{
    case Online = 'online';
    case Offline = 'offline';
    case Simulation = 'simulation';
}
