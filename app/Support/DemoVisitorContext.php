<?php

namespace App\Support;

use Illuminate\Http\Request;
use RuntimeException;

final class DemoVisitorContext
{
    public function __construct(private readonly Request $request)
    {
    }

    public function enabled(): bool
    {
        return (bool) config('demo.visitor_isolation_enabled', false);
    }

    public function stateKey(): string
    {
        if (! $this->enabled()) {
            return 'scada.simulation.state';
        }

        $id = $this->request->attributes->get('scada_demo_visitor_id');

        if (! is_string($id)) {
            // Never fall back to a shared cache key when isolation is enabled.
            throw new RuntimeException('Missing anonymous demo visitor context.');
        }

        return DemoStateKey::forVisitor($id);
    }
}
