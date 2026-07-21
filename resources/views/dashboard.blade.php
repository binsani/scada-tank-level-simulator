@extends('layouts.app')

@section('content')
@php
    $tank = $snapshot['tank'] ?? [];
    $pump = $snapshot['pump'] ?? [];
    $state = $state ?? $snapshot ?? [];
@endphp

<div class="space-y-4">
    <header class="scada-panel flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl border border-cyan-400/30 bg-cyan-500/10 text-xl font-black text-cyan-300">TL</div>
            <div>
                <div class="scada-label">Tank Level SCADA Simulator</div>
                <h1 class="text-2xl font-bold tracking-wide text-white">Industrial Water Tank Control Room</h1>
            </div>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            <div class="scada-panel bg-slate-950/60">
                <div class="scada-label">Date</div>
                <div class="scada-value">{{ now()->format('d M Y') }}</div>
            </div>
            <div class="scada-panel bg-slate-950/60">
                <div class="scada-label">Time</div>
                <div class="scada-value">{{ now()->format('H:i:s') }}</div>
            </div>
            <div class="scada-panel bg-slate-950/60">
                <div class="scada-label">Operator</div>
                <div class="scada-value">{{ auth()->user()->name ?? 'Simulation Operator' }}</div>
            </div>
            <div class="scada-panel bg-slate-950/60">
                <div class="scada-label">System</div>
                <div class="status-led text-cyan-300 before:bg-cyan-400">{{ strtoupper($state['system_status'] ?? 'simulation') }}</div>
            </div>
            <div class="scada-panel bg-slate-950/60">
                <div class="scada-label">Mode</div>
                <div class="status-led text-emerald-300 before:bg-emerald-400">{{ strtoupper($state['operating_mode'] ?? 'auto') }}</div>
            </div>
        </div>
    </header>

    <div class="grid gap-4 xl:grid-cols-[320px_minmax(0,1fr)_340px]">
        <aside class="scada-panel space-y-4">
            <div>
                <div class="scada-label">Tank Information</div>
                <h2 class="text-xl font-semibold text-white">{{ $tank['name'] ?? 'Process Tank A-101' }}</h2>
            </div>
            @foreach([
                'Tank Capacity' => number_format($tank['capacity_liters'] ?? 1000).' L',
                'Current Water Level' => number_format($tank['level_percent'] ?? 35, 1).'%',
                'Percentage Filled' => number_format($tank['level_percent'] ?? 35, 1).'%',
                'Current Volume' => number_format($tank['volume_liters'] ?? 350).' L',
                'Temperature' => number_format($tank['temperature_c'] ?? 27.3, 1).' °C',
                'Pressure' => number_format($tank['pressure_bar'] ?? 1.8, 2).' bar',
                'Flow Rate' => number_format($tank['flow_rate_l_min'] ?? 18.4, 1).' L/min',
                'Pump Status' => strtoupper($pump['state'] ?? 'stopped'),
                'Valve Status' => strtoupper($state['valves']['inlet'] ?? 'closed').' / '.strtoupper($state['valves']['outlet'] ?? 'closed'),
                'Operating Mode' => strtoupper($state['operating_mode'] ?? 'auto'),
            ] as $label => $value)
                <div class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950/60 px-4 py-3">
                    <span class="text-sm text-slate-400">{{ $label }}</span>
                    <span class="text-sm font-semibold text-white">{{ $value }}</span>
                </div>
            @endforeach
        </aside>

        <main class="scada-panel relative overflow-hidden">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_center,_rgba(56,189,248,0.14),_transparent_55%)]"></div>
            <div class="relative grid min-h-[680px] gap-4 lg:grid-cols-[180px_minmax(0,1fr)_180px]">
                <div class="flex flex-col justify-between gap-4 text-xs text-slate-400">
                    @foreach([100, 75, 50, 25, 0] as $mark)
                        <div class="flex items-center gap-3">
                            <span class="w-10 text-right">{{ $mark }}%</span>
                            <span class="h-px flex-1 bg-slate-700"></span>
                        </div>
                    @endforeach
                </div>
                <div class="relative mx-auto flex w-full max-w-[560px] items-center justify-center">
                    <div class="relative h-[600px] w-[260px] rounded-[3rem] border-4 border-slate-600 bg-slate-950 shadow-[inset_0_0_50px_rgba(15,23,42,0.95)]">
                        <div class="absolute inset-x-6 bottom-6 top-6 overflow-hidden rounded-[2.3rem] border border-cyan-400/20 bg-slate-900/70">
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-cyan-500 via-sky-400 to-cyan-300 transition-all duration-1000 ease-out animate-ripple" style="height: {{ $tank['level_percent'] ?? 35 }}%;"></div>
                            <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.05)_1px,transparent_1px)] bg-[length:100%_32px]"></div>
                            <div class="absolute inset-0 flex items-end justify-center pb-10 text-3xl font-black text-white drop-shadow-lg">{{ number_format($tank['level_percent'] ?? 35, 1) }}%</div>
                            <div class="absolute left-4 right-4 top-8 flex justify-between text-[10px] uppercase tracking-[0.3em] text-slate-300">
                                <span>Tank</span>
                                <span>1000 L</span>
                            </div>
                        </div>
                        <div class="absolute -left-16 top-28 h-6 w-20 rounded-full border border-slate-500 bg-slate-800"></div>
                        <div class="absolute -right-16 bottom-24 h-6 w-20 rounded-full border border-slate-500 bg-slate-800"></div>
                        <div class="absolute left-1/2 top-2 h-8 w-8 -translate-x-1/2 rounded-full border-2 border-cyan-300 bg-slate-900"></div>
                    </div>
                    <div class="absolute left-4 top-10 rounded-full border border-emerald-400/40 bg-emerald-500/10 px-4 py-2 text-sm text-emerald-300">Live Level</div>
                </div>
                <div class="space-y-4 text-sm">
                    <div class="rounded-2xl border border-slate-700 bg-slate-950/70 p-4">
                        <div class="scada-label mb-3">Process Status</div>
                        <div class="space-y-2 text-slate-300">
                            <div>Flow: {{ strtoupper($state['process'] ?? 'idle') }}</div>
                            <div>Pump: {{ strtoupper($pump['state'] ?? 'stopped') }}</div>
                            <div>Inlet Valve: {{ strtoupper($state['valves']['inlet'] ?? 'closed') }}</div>
                            <div>Outlet Valve: {{ strtoupper($state['valves']['outlet'] ?? 'closed') }}</div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-slate-700 bg-slate-950/70 p-4">
                        <div class="scada-label mb-3">Alarm Window</div>
                        <div class="space-y-2">
                            @forelse(($snapshot['alarms'] ?? []) as $alarm)
                                <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-3 py-2 text-red-200">{{ $alarm['message'] }}</div>
                            @empty
                                <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 text-emerald-200">No active alarms</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <aside class="scada-panel space-y-4">
            <div>
                <div class="scada-label">Operator Controls</div>
                <h2 class="text-xl font-semibold text-white">Process Commands</h2>
            </div>
            <div class="grid gap-3">
                <form method="POST" action="/api/start">@csrf<button class="w-full rounded-xl bg-emerald-500 px-4 py-3 font-semibold text-slate-950 transition hover:bg-emerald-400">Start Filling</button></form>
                <form method="POST" action="/api/stop">@csrf<button class="w-full rounded-xl bg-slate-700 px-4 py-3 font-semibold text-white transition hover:bg-slate-600">Stop Filling</button></form>
                <form method="POST" action="/api/reset">@csrf<button class="w-full rounded-xl bg-cyan-500 px-4 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400">Reset Simulation</button></form>
            </div>
            <div class="space-y-4 rounded-2xl border border-slate-700 bg-slate-950/70 p-4 text-sm text-slate-300">
                <label class="block">
                    <span class="mb-2 block text-xs uppercase tracking-[0.3em] text-slate-400">Fill Speed</span>
                    <input type="range" min="0" max="10" value="5" class="w-full accent-cyan-400">
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs uppercase tracking-[0.3em] text-slate-400">Drain Speed</span>
                    <input type="range" min="0" max="10" value="4" class="w-full accent-amber-400">
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs uppercase tracking-[0.3em] text-slate-400">Simulation Speed</span>
                    <input type="range" min="0.5" max="5" step="0.5" value="1" class="w-full accent-lime-400">
                </label>
            </div>
        </aside>
    </div>

    <section class="grid gap-4 xl:grid-cols-2">
        <div class="scada-panel">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <div class="scada-label">Live Trends</div>
                    <h2 class="text-xl font-semibold text-white">Process analytics</h2>
                </div>
                <div class="text-sm text-slate-400">Last 30 Minutes</div>
            </div>
            <div class="grid gap-4 lg:grid-cols-2">
                <div class="h-48 rounded-xl border border-slate-800 bg-slate-950/70 p-3"><canvas id="levelChart"></canvas></div>
                <div class="h-48 rounded-xl border border-slate-800 bg-slate-950/70 p-3"><canvas id="pressureChart"></canvas></div>
                <div class="h-48 rounded-xl border border-slate-800 bg-slate-950/70 p-3"><canvas id="temperatureChart"></canvas></div>
                <div class="h-48 rounded-xl border border-slate-800 bg-slate-950/70 p-3"><canvas id="flowChart"></canvas></div>
                <div class="h-48 rounded-xl border border-slate-800 bg-slate-950/70 p-3"><canvas id="currentChart"></canvas></div>
                <div class="h-48 rounded-xl border border-slate-800 bg-slate-950/70 p-3"><canvas id="energyChart"></canvas></div>
            </div>
        </div>
        <div class="scada-panel space-y-4">
            <div>
                <div class="scada-label">Event Log</div>
                <h2 class="text-xl font-semibold text-white">Recent operator and process events</h2>
            </div>
            <div class="space-y-3">
                <div class="rounded-xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-sm text-slate-300">Pump Started - Filling mode activated</div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-sm text-slate-300">Valve Opened - Inlet valve is in opening state</div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-sm text-slate-300">Alarm Triggered - High level threshold at 80%</div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-sm text-slate-300">Simulation Reset - Defaults restored</div>
            </div>
        </div>
    </section>
</div>
@endsection
