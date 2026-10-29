<?php

// Dependency-free static contract checks; Laravel integration tests are still required.
$source = file_get_contents(__DIR__.'/../../app/Support/DemoVisitorContext.php');
if ($source === false
    || ! str_contains($source, "return 'scada.simulation.state';")
    || ! str_contains($source, "DemoStateKey::forVisitor(\$id)")
    || ! str_contains($source, "throw new RuntimeException('Missing anonymous demo visitor context.')")) {
    fwrite(STDERR, "Visitor context fail-closed contract missing.\n");
    exit(1);
}
echo "Visitor context static contract passed.\n";
