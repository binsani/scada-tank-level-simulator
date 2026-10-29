<?php

// Dependency-free checks until Laravel integration tests are available.
$root = dirname(__DIR__, 2);
$files = [
    'scope' => file_get_contents($root.'/app/Support/DemoHistoryScope.php'),
    'alarms' => file_get_contents($root.'/app/Http/Controllers/AlarmController.php'),
    'reports' => file_get_contents($root.'/app/Http/Controllers/ReportController.php'),
    'api' => file_get_contents($root.'/routes/api.php'),
    'console' => file_get_contents($root.'/routes/console.php'),
    'context' => file_get_contents($root.'/app/Support/DemoVisitorContext.php'),
];
$checks = [
    'history queries filter by visitor key' => str_contains($files['scope'], "where('demo_visitor_key',"),
    'alarm listing is scoped' => str_contains($files['alarms'], 'historyScope->apply(Alarm::query())'),
    'alarm mutations enforce ownership' => substr_count($files['alarms'], 'historyScope->ownsRecord(') >= 2,
    'report exports are scoped' => str_contains($files['reports'], 'historyScope->apply($query)'),
    'event API is scoped' => str_contains($files['api'], 'DemoHistoryScope'),
    'CLI tick blocked in isolation mode' => str_contains($files['console'], "config('demo.visitor_isolation_enabled', false)"),
    'visitor context fails closed' => str_contains($files['context'], 'Missing anonymous demo visitor context.'),
];
foreach ($checks as $description => $passed) {
    if (! $passed) {
        fwrite(STDERR, "FAIL: {$description}\n");
        exit(1);
    }
}
echo "Demo isolation static contracts passed.\n";
