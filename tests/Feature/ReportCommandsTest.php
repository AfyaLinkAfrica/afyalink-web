<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_says_so_when_there_are_no_reports(): void
    {
        $this->artisan('reports:list')
            ->expectsOutput('No reports to show.')
            ->assertExitCode(0);
    }

    public function test_list_shows_new_reports_by_default(): void
    {
        $facility = Facility::factory()->create(['name' => 'Listed Clinic']);
        $report = Report::factory()->create([
            'facility_id' => $facility->id,
            'message' => 'The clinic has moved.',
            'contact' => 'caller@example.com',
        ]);

        $this->artisan('reports:list')
            ->expectsTable(
                ['ID', 'Facility', 'Status', 'Received', 'Contact', 'Message'],
                [[$report->id, 'Listed Clinic', 'new', $report->created_at->format('Y-m-d H:i'), 'caller@example.com', 'The clinic has moved.']],
            )
            ->assertExitCode(0);
    }

    public function test_list_hides_resolved_reports_unless_all_is_requested(): void
    {
        Report::factory()->resolved()->create();

        $this->artisan('reports:list')
            ->expectsOutput('No reports to show.')
            ->assertExitCode(0);

        $this->artisan('reports:list', ['--all' => true])
            ->assertExitCode(0);
    }

    public function test_resolve_marks_a_report_as_resolved(): void
    {
        $report = Report::factory()->create();

        $this->artisan('reports:resolve', ['id' => $report->id])->assertExitCode(0);

        $this->assertDatabaseHas('reports', ['id' => $report->id, 'status' => Report::STATUS_RESOLVED]);
    }

    public function test_resolve_fails_for_an_unknown_report(): void
    {
        $this->artisan('reports:resolve', ['id' => 999999])
            ->expectsOutput('No report with that ID.')
            ->assertExitCode(1);
    }
}
