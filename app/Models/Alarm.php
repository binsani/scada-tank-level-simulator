<?php

namespace App\Models;

use App\Enums\AlarmPriority;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alarm extends Model
{
    use HasFactory;

    protected $fillable = [
        'demo_visitor_key',
        'tank_id',
        'priority',
        'message',
        'equipment',
        'status',
        'acknowledged_by',
        'acknowledged_at',
        'triggered_at',
    ];

    protected $casts = [
        'priority' => AlarmPriority::class,
        'acknowledged_at' => 'datetime',
        'triggered_at' => 'datetime',
    ];
}
