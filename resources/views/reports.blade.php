@extends('layouts.app')

@section('content')
<div class="scada-panel space-y-4">
    <div>
        <div class="scada-label">Reports</div>
        <h1 class="text-2xl font-bold text-white">PDF, CSV, and Excel reporting</h1>
    </div>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4">Daily Operation Report</div>
        <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4">Alarm Report</div>
        <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4">Pump Performance Report</div>
        <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4">Tank Utilization Report</div>
        <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4">Water Consumption Report</div>
    </div>
</div>
@endsection
