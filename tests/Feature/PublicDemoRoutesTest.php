<?php

// Dependency-free route contract check; run with: php tests/Feature/PublicDemoRoutesTest.php
$routes = file_get_contents(__DIR__.'/../../routes/api.php');
if ($routes === false) {
    fwrite(STDERR, "Cannot read API routes.\n");
    exit(1);
}
foreach (['/start', '/stop', '/reset', '/manual', '/auto'] as $path) {
    if (!str_contains($routes, "Route::post('".$path."'")) {
        fwrite(STDERR, "Missing public simulation route: {$path}\n");
        exit(1);
    }
}
if (!str_contains($routes, "throttle:30,1") || !str_contains($routes, "Route::get('/health'")) {
    fwrite(STDERR, "Expected API throttle or health route missing.\n");
    exit(1);
}
echo "Public demo route contract checks passed.\n";
