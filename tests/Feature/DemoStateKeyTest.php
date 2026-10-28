<?php

// Dependency-free foundational checks, not Laravel integration tests.
// Run: php tests/Feature/DemoStateKeyTest.php
require_once __DIR__.'/../../app/Support/DemoStateKey.php';

use App\Support\DemoStateKey;

$a = DemoStateKey::newVisitorId();
$b = DemoStateKey::newVisitorId();
if ($a === $b || strlen($a) !== 64 || strlen($b) !== 64) {
    fwrite(STDERR, "Visitor IDs are not unique 64-character hex values.\n");
    exit(1);
}
if (DemoStateKey::forVisitor($a) === DemoStateKey::forVisitor($b)) {
    fwrite(STDERR, "Two visitors resolved to the same state key.\n");
    exit(1);
}
foreach (['', 'shared', '../evil', str_repeat('a', 63), strtoupper($a)] as $invalid) {
    try {
        DemoStateKey::forVisitor($invalid);
        fwrite(STDERR, "Invalid visitor identifier accepted.\n");
        exit(1);
    } catch (InvalidArgumentException $expected) {
        // Correctly rejected.
    }
}
echo "Demo state key checks passed.\n";
