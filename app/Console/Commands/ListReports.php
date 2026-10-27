<?php

namespace App\Console\Commands;

use App\Models\Report;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ListReports extends Command
{
    protected $signature = 'reports:list {--all : Include resolved reports}';

    protected $description = 'List reports of incorrect facility information (new ones by default)';

    public function handle(): int
    {
        $reports = Report::query()
            ->with('facility:id,name')
            ->when(! $this->option('all'), fn ($query) => $query->where('status', Report::STATUS_NEW))
            ->oldest()
            ->get();

        if ($reports->isEmpty()) {
            $this->info('No reports to show.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Facility', 'Status', 'Received', 'Contact', 'Message'],
            $reports->map(fn (Report $report) => [
                $report->id,
                $report->facility->name,
                $report->status,
                $report->created_at->format('Y-m-d H:i'),
                $report->contact ?? '-',
                Str::limit($report->message, 120),
            ])->all(),
        );

        return self::SUCCESS;
    }
}
