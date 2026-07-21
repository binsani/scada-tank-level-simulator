<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PumpLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tank_id',
        'pump_state',
        'motor_current_a',
        'motor_temperature_c',
        'energy_used_kwh',
        'logged_at',
    ];

    protected $casts = [
        'motor_current_a' => 'float',
        'motor_temperature_c' => 'float',
        'energy_used_kwh' => 'float',
        'logged_at' => 'datetime',
    ];
}
