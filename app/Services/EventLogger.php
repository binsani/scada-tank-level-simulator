<?php

namespace App\Services;

use App\Support\DemoVisitorContext;

use App\Models\Event;

class EventLogger
{
    public function __construct(private readonly DemoVisitorContext $visitorContext)
    {
    }

    public function record(string $type, string $message, array $meta = []): ?Event
    {
        try {
            return Event::create([
                'demo_visitor_key' => $this->visitorContext->historyKey(),
                'type' => $type,
                'message' => $message,
                'meta' => $meta,
                'occurred_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('EventLogger failed: '.$e->getMessage());
            return null;
        }
    }
}
