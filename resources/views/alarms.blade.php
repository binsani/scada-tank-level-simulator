@extends('layouts.app')

@section('content')
<div class="scada-panel space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <div class="scada-label">Alarm Management</div>
            <h1 class="text-2xl font-bold text-white">Active and historical alarms</h1>
        </div>
        <div class="rounded-full border border-amber-400/30 bg-amber-500/10 px-4 py-2 text-amber-300">Filter | Search | Export</div>
    </div>
    <div class="overflow-hidden rounded-2xl border border-slate-800">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-950/80 text-slate-400">
                <tr>
                    <th class="px-4 py-3">Time</th>
                    <th class="px-4 py-3">Priority</th>
                    <th class="px-4 py-3">Message</th>
                    <th class="px-4 py-3">Equipment</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Acknowledged By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 bg-slate-900/70 text-slate-200">
                <tr>
                    <td class="px-4 py-3">{{ now()->format('H:i:s') }}</td>
                    <td class="px-4 py-3 text-red-300">Critical</td>
                    <td class="px-4 py-3">HIGH HIGH LEVEL ALARM</td>
                    <td class="px-4 py-3">Tank</td>
                    <td class="px-4 py-3">Active</td>
                    <td class="px-4 py-3">Operator</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
