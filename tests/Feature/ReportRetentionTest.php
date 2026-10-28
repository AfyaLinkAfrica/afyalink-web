<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReportRetentionTest extends TestCase
{
    use RefreshDatabase;

    private function report(string $status, ?Carbon $resolvedAt = null): Report
    {
        return Report::factory()
            ->for(Facility::factory()->create())
            ->create([
                'status' => $status,
                'resolved_at' => $resolvedAt,
            ]);
    }

    public function test_resolving_a_report_records_when_it_was_resolved(): void
    {
        $report = $this->report(Report::STATUS_NEW);

        $this->artisan('reports:resolve', ['id' => $report->id])->assertExitCode(0);

        $report->refresh();
        $this->assertSame(Report::STATUS_RESOLVED, $report->status);
        $this->assertNotNull($report->resolved_at);
    }

    public function test_resolving_twice_does_not_restart_the_retention_clock(): void
    {
        $original = Carbon::parse('2026-10-01 09:00:00');
        $report = $this->report(Report::STATUS_RESOLVED, $original);

        $this->artisan('reports:resolve', ['id' => $report->id])
            ->expectsOutputToContain('already resolved')
            ->assertExitCode(0);

        $this->assertTrue($report->fresh()->resolved_at->equalTo($original));
    }

    public function test_resolved_reports_older_than_90_days_are_deleted(): void
    {
        $old = $this->report(Report::STATUS_RESOLVED, now()->subDays(91));

        $this->artisan('reports:prune')
            ->expectsOutputToContain('Deleted 1 resolved report')
            ->assertExitCode(0);

        $this->assertModelMissing($old);
    }

    public function test_recently_resolved_reports_are_kept(): void
    {
        $recent = $this->report(Report::STATUS_RESOLVED, now()->subDays(89));

        $this->artisan('reports:prune')->assertExitCode(0);

        $this->assertModelExists($recent);
    }

    public function test_new_reports_are_never_deleted_however_old(): void
    {
        $waiting = $this->report(Report::STATUS_NEW);
        $waiting->forceFill(['created_at' => now()->subDays(400)])->save();

        $this->artisan('reports:prune')->assertExitCode(0);

        $this->assertModelExists($waiting);
    }

    public function test_a_resolved_report_without_a_resolution_date_is_kept(): void
    {
        $undated = $this->report(Report::STATUS_RESOLVED, null);

        $this->artisan('reports:prune')->assertExitCode(0);

        $this->assertModelExists($undated);
    }

    public function test_a_dry_run_reports_but_deletes_nothing(): void
    {
        $old = $this->report(Report::STATUS_RESOLVED, now()->subDays(120));

        $this->artisan('reports:prune', ['--dry-run' => true])
            ->expectsOutputToContain('Would delete 1 resolved report')
            ->assertExitCode(0);

        $this->assertModelExists($old);
    }

    public function test_the_retention_period_can_be_changed(): void
    {
        $report = $this->report(Report::STATUS_RESOLVED, now()->subDays(40));

        $this->artisan('reports:prune', ['--days' => 30])->assertExitCode(0);

        $this->assertModelMissing($report);
    }

    public function test_a_bad_retention_period_is_refused_and_nothing_is_deleted(): void
    {
        $old = $this->report(Report::STATUS_RESOLVED, now()->subDays(500));

        $this->artisan('reports:prune', ['--days' => 'abc'])->assertExitCode(1);
        $this->artisan('reports:prune', ['--days' => 0])->assertExitCode(1);

        $this->assertModelExists($old);
    }

    public function test_the_clean_up_runs_every_day(): void
    {
        $this->artisan('schedule:list')
            ->expectsOutputToContain('reports:prune')
            ->assertExitCode(0);
    }
}
