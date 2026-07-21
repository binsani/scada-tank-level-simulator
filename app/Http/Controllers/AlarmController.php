<?php

namespace App\Http\Controllers;

use App\Contracts\TankDataProvider;
use App\Models\Alarm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AlarmController extends Controller
{
    public function __construct(private readonly TankDataProvider $tankDataProvider)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Alarm::query()->latest()->limit(50)->get(),
            'snapshot' => $this->tankDataProvider->snapshot(),
        ]);
    }

    public function acknowledge(Alarm $alarm, Request $request): JsonResponse
    {
        $alarm->update([
            'status' => 'acknowledged',
            'acknowledged_by' => $request->user()?->name ?? 'Operator',
            'acknowledged_at' => now(),
        ]);

        return response()->json(['message' => 'Alarm acknowledged.', 'alarm' => $alarm]);
    }

    public function clear(Alarm $alarm): JsonResponse
    {
        $alarm->update(['status' => 'cleared']);

        return response()->json(['message' => 'Alarm cleared.', 'alarm' => $alarm]);
    }

    public function export(): JsonResponse
    {
        return response()->json([
            'message' => 'Alarm export endpoint ready for CSV or Excel export integration.',
        ]);
    }
}
