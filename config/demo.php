<?php

return [
    // Keep disabled until API/web cookies, history, and background ticks are fully isolated.
    'visitor_isolation_enabled' => (bool) env('SCADA_VISITOR_ISOLATION_ENABLED', false),
    'visitor_ttl_minutes' => 120,
    'max_catch_up_ticks' => 60,
];
