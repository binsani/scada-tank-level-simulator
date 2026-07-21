<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity_liters',
        'level_percent',
        'temperature_c',
        'pressure_bar',
        'flow_rate_l_min',
        'operating_mode',
        'system_status',
        'is_overflowing',
    ];

    protected $casts = [
        'capacity_liters' => 'integer',
        'level_percent' => 'float',
        'temperature_c' => 'float',
        'pressure_bar' => 'float',
        'flow_rate_l_min' => 'float',
        'is_overflowing' => 'boolean',
    ];

    public function sensors(): HasMany
    {
        return $this->hasMany(Sensor::class);
    }

    public function pumpLogs(): HasMany
    {
        return $this->hasMany(PumpLog::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
