<?php

namespace App\Services;

use App\Models\Event;

class EventLogger
{
    public function record(string $type, string $message, array $meta = []): ?Event
    {
        try {
            return Event::create([
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
