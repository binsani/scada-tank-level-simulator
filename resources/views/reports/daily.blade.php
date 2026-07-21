@extends('layouts.app')

@section('content')
<div class="scada-panel space-y-4">
    <div>
        <div class="scada-label">Daily Operation Report</div>
        <h1 class="text-2xl font-bold text-white">Operational summary</h1>
    </div>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4">Tank utilization</div>
        <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4">Alarms generated</div>
        <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4">Pump runtime</div>
        <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4">Water consumed</div>
    </div>
</div>
@endsection
