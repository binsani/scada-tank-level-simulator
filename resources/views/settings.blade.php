@extends('layouts.app')

@section('content')
<div class="scada-panel space-y-6">
    <div>
        <div class="scada-label">Settings</div>
        <h1 class="text-2xl font-bold text-white">Simulation, alarms, and units</h1>
    </div>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach([
            'Tank Capacity',
            'Simulation Speed',
            'Alarm Thresholds',
            'Sensor Noise',
            'Temperature Range',
            'Pressure Range',
            'Units',
        ] as $setting)
            <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4">
                <div class="scada-label">{{ $setting }}</div>
                <div class="mt-2 text-white">Editable through the settings API</div>
            </div>
        @endforeach
    </div>
</div>
@endsection
