<?php

namespace App\Console\Commands;

use App\Models\Report;
use Illuminate\Console\Command;

class ResolveReport extends Command
{
    protected $signature = 'reports:resolve {id : The report ID}';

    protected $description = 'Mark a report as resolved';

    public function handle(): int
    {
        $report = Report::find($this->argument('id'));

        if ($report === null) {
            $this->error('No report with that ID.');

            return self::FAILURE;
        }

        // Resolving twice must not restart the 90-day retention clock.
        if ($report->status === Report::STATUS_RESOLVED) {
            $this->info("Report {$report->id} was already resolved.");

            return self::SUCCESS;
        }

        $report->update([
            'status' => Report::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);
        $this->info("Report {$report->id} marked as resolved.");

        return self::SUCCESS;
    }
}
