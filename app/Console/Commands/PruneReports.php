<?php

namespace App\Console\Commands;

use App\Models\Report;
use Illuminate\Console\Command;

class PruneReports extends Command
{
    protected $signature = 'reports:prune
        {--days=90 : Delete resolved reports resolved more than this many days ago}
        {--dry-run : Show how many reports would be deleted, without deleting them}';

    protected $description = 'Delete resolved reports once the retention period has passed';

    public function handle(): int
    {
        $days = filter_var($this->option('days'), FILTER_VALIDATE_INT);

        if ($days === false || $days < 1) {
            $this->error('--days must be a whole number of 1 or more.');

            return self::FAILURE;
        }

        // Only resolved reports with a resolution date are ever deleted. New reports are kept.
        $expired = Report::query()
            ->where('status', Report::STATUS_RESOLVED)
            ->where('resolved_at', '<', now()->subDays($days));

        if ($this->option('dry-run')) {
            $count = $expired->count();
            $this->info("Would delete {$count} resolved report(s) resolved more than {$days} days ago. Nothing was deleted.");

            return self::SUCCESS;
        }

        $count = $expired->delete();
        $this->info("Deleted {$count} resolved report(s) resolved more than {$days} days ago.");

        return self::SUCCESS;
    }
}
