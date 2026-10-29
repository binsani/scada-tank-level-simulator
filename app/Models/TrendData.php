<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrendData extends Model
{
    use HasFactory;

    protected $fillable = [
        'demo_visitor_key',
        'tank_level',
        'pressure',
        'temperature',
        'flow_rate',
        'motor_current',
        'energy_consumption',
        'recorded_at',
    ];

    protected $casts = [
        'tank_level' => 'float',
        'pressure' => 'float',
        'temperature' => 'float',
        'flow_rate' => 'float',
        'motor_current' => 'float',
        'energy_consumption' => 'float',
        'recorded_at' => 'datetime',
    ];
}
