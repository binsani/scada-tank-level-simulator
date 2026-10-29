<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

final class DemoHistoryScope
{
    public function __construct(private readonly DemoVisitorContext $visitorContext)
    {
    }

    public function apply(Builder $query): Builder
    {
        // Legacy mode must never display isolated visitors' records.
        return $query->where('demo_visitor_key', $this->visitorContext->historyKey());
    }

    public function ownsRecord(?string $recordKey): bool
    {
        return $recordKey === $this->visitorContext->historyKey();
    }
}
