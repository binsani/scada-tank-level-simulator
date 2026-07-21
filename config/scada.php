<?php

return [
    'default_tank_capacity_liters' => 1000,
    'default_level_percent' => 35,
    'default_temperature_c' => [24, 29],
    'default_pressure_bar' => [1.2, 2.6],
    'default_flow_l_min' => [8, 36],
    'default_motor_current_a' => [6.0, 11.5],
    'default_motor_temperature_c' => [40, 63],
    'alarm_thresholds' => [
        'low_low' => 5,
        'low' => 15,
        'high' => 80,
        'high_high' => 95,
    ],
    'sensor_noise' => [
        'level' => 0.5,
        'pressure' => 0.08,
        'temperature' => 0.4,
        'flow' => 1.1,
        'current' => 0.25,
        'motor_temperature' => 0.4,
    ],
    'simulation_speed' => 1.0,
    'units' => 'metric',
];
