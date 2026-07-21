<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsUpdateRequest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('settings', [
            'settings' => Setting::query()->orderBy('group')->orderBy('key')->get(),
        ]);
    }

    public function update(SettingsUpdateRequest $request): JsonResponse
    {
        foreach ($request->validated()['settings'] as $key => $value) {
            Setting::query()->updateOrCreate([
                'key' => $key,
            ], [
                'value' => $value,
                'group' => $request->validated()['group'] ?? 'simulation',
            ]);
        }

        return response()->json(['message' => 'Settings updated successfully.']);
    }
}
