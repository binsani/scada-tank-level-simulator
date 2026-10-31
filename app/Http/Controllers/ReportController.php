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
        $validated = $request->validate([
            'type' => ['sometimes', 'string', 'in:events,alarms,trends'],
            'format' => ['sometimes', 'string', 'in:csv,json'],
        ]);
        $type = $validated['type'] ?? 'events';
        $format = $validated['format'] ?? 'csv';

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
                    fputcsv($file, array_map(static function ($value) {
                        // Preserve structured metadata and prevent spreadsheet formula execution.
                        if (is_array($value)) {
                            $value = json_encode($value, JSON_THROW_ON_ERROR);
                        }

                        if (is_string($value) && preg_match('/\A[\x00-\x20]*[=+@-]/', $value)) {
                            return "'".$value;
                        }

                        return $value;
                    }, $row->toArray()));
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

