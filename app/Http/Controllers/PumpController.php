<?php

namespace App\Http\Controllers;

use App\Contracts\TankDataProvider;
use Illuminate\Http\JsonResponse;

class PumpController extends Controller
{
    public function __construct(private readonly TankDataProvider $tankDataProvider)
    {
    }

    public function show(): JsonResponse
    {
        return response()->json(data_get($this->tankDataProvider->snapshot(), 'pump', []));
    }
}
