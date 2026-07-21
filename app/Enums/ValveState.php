<?php

namespace App\Enums;

enum ValveState: string
{
    case Open = 'open';
    case Closed = 'closed';
    case Opening = 'opening';
    case Closing = 'closing';
    case Fault = 'fault';
}
