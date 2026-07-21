<?php

namespace App\Models;

use App\Enums\PumpState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pump extends Model
{
    use HasFactory;

    protected $fillable = [
        'tank_id',
        'name',
        'state',
        'running_hours',
        'motor_current_a',
        'motor_temperature_c',
        'energy_used_kwh',
        'efficiency_percent',
    ];

    protected $casts = [
        'state' => PumpState::class,
        'running_hours' => 'float',
        'motor_current_a' => 'float',
        'motor_temperature_c' => 'float',
        'energy_used_kwh' => 'float',
        'efficiency_percent' => 'float',
    ];
}
