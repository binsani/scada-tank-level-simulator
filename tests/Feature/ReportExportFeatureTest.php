<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Support\DemoStateKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_export_parameters_return_validation_errors(): void
    {
        foreach (['type=unknown', 'type[]=events', 'format[]=csv', 'format=pdf', 'type=events%0D%0AX-Test%3Ayes'] as $query) {
            $this->getJson('/reports/export?'.$query)->assertUnprocessable();
        }
    }

    public function test_csv_neutralizes_formulas_and_serializes_metadata(): void
    {
        config(['demo.visitor_isolation_enabled' => false]);

        foreach (['=1+1', '+1+1', '-1+1', '@SUM(1)', " \t=1+1"] as $message) {
            Event::create(['type' => 'test', 'message' => $message, 'meta' => ['action' => 'start']]);
        }

        $response = $this->get('/reports/export');
        $response->assertOk();
        $this->assertStringContainsString('attachment; filename=scada_events_report_', $response->headers->get('Content-Disposition'));

        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $response->streamedContent());
        rewind($stream);
        $headers = fgetcsv($stream);
        $messages = [];
        while (($row = fgetcsv($stream)) !== false) {
            $record = array_combine($headers, $row);
            $messages[] = $record['message'];
            $this->assertSame(['action' => 'start'], json_decode($record['meta'], true));
        }
        fclose($stream);

        $this->assertEqualsCanonicalizing(["'=1+1", "'+1+1", "'-1+1", "'@SUM(1)", "' \t=1+1"], $messages);
    }

    public function test_exports_preserve_visitor_isolation_and_json_values(): void
    {
        config(['demo.visitor_isolation_enabled' => true]);
        $visitor = DemoStateKey::newVisitorId();
        Event::create(['type' => 'own', 'message' => '=1+1', 'demo_visitor_key' => hash('sha256', DemoStateKey::forVisitor($visitor))]);
        Event::create(['type' => 'other', 'message' => 'Other visitor', 'demo_visitor_key' => str_repeat('b', 64)]);
        Event::create(['type' => 'legacy', 'message' => 'Shared history']);

        $this->withCookie('scada_demo_visitor', $visitor)->withCredentials()
            ->getJson('/reports/export?format=json')
            ->assertOk()->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.message', '=1+1');

        $this->app->forgetScopedInstances();
        $response = $this->withCookie('scada_demo_visitor', $visitor)->withCredentials()
            ->get('/reports/export?format=csv');
        $response->assertOk();
        $csv = $response->streamedContent();
        $this->assertStringContainsString("'=1+1", $csv);
        $this->assertStringNotContainsString('Other visitor', $csv);
        $this->assertStringNotContainsString('Shared history', $csv);
    }
}
