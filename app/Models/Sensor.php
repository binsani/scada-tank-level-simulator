<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

    protected $fillable = [
        'tank_id',
        'name',
        'type',
        'unit',
        'value',
        'min_value',
        'max_value',
        'status',
    ];

    protected $casts = [
        'value' => 'float',
        'min_value' => 'float',
        'max_value' => 'float',
    ];
}
