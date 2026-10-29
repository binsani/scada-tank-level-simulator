<?php

namespace App\Http\Controllers;

use App\Support\DemoHistoryScope;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private readonly DemoHistoryScope $historyScope)
    {
    }

    public function index(): View
    {
        return view('reports', [
            'pageTitle' => 'SCADA Reports',
        ]);
    }

    public function dailyOperation(): View
    {
        return view('reports.daily', ['title' => 'Daily Operation Report']);
    }

    public function export(Request $request): mixed
    {
        $type = $request->query('type', 'events');
        $format = strtolower($request->query('format', 'csv'));

        $query = match ($type) {
            'alarms' => \App\Models\Alarm::query()->latest(),
            'trends' => \App\Models\TrendData::query()->latest(),
            default => \App\Models\Event::query()->latest(),
        };

        $data = $this->historyScope->apply($query)->limit(500)->get();

        if ($format === 'json') {
            return response()->json([
                'report_type' => $type,
                'generated_at' => now()->toIso8601String(),
                'count' => $data->count(),
                'data' => $data,
            ]);
        }

        $filename = "scada_{$type}_report_" . date('Y-m-d_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            if ($data->isNotEmpty()) {
                fputcsv($file, array_keys($data->first()->toArray()));
                foreach ($data as $row) {
                    fputcsv($file, $row->toArray());
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(): mixed
    {
        return response()->json(['message' => 'PDF export endpoint is scaffolded.']);
    }
}

