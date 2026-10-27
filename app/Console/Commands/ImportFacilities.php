<?php

namespace App\Console\Commands;

use App\Models\Facility;
use App\Services\FacilityCsvImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ImportFacilities extends Command
{
    protected $signature = 'facilities:import
        {file : Path to the CSV file}
        {--source= : Name of the data source, as recorded in docs/data-sources.md}
        {--confirm-licence : Confirm the source licence allows this import and is recorded}
        {--dry-run : Check the file and show what would change, without writing anything}
        {--remove-demo-data : After a successful import, delete the synthetic demo facilities}';

    protected $description = 'Import facilities from a CSV file (all rows are checked first; nothing is written if any row is invalid)';

    public function handle(FacilityCsvImporter $importer): int
    {
        $source = trim((string) $this->option('source'));
        $dryRun = (bool) $this->option('dry-run');

        if ($source === '') {
            $this->error('Please give the data source with --source="Name of the source".');

            return self::FAILURE;
        }

        if (strtolower($source) === 'synthetic') {
            $this->error('The source name "synthetic" is reserved for demo data.');

            return self::FAILURE;
        }

        if (! $dryRun && ! $this->option('confirm-licence')) {
            $this->error('Record the source and its licence in docs/data-sources.md, then add --confirm-licence.');

            return self::FAILURE;
        }

        try {
            $result = $importer->read((string) $this->argument('file'));
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        if ($result['errors'] !== []) {
            $this->error('The file has problems. Nothing was imported.');

            foreach (array_slice($result['errors'], 0, 25) as $problem) {
                $this->line("  {$problem}");
            }

            if (count($result['errors']) > 25) {
                $this->line('  ... and '.(count($result['errors']) - 25).' more.');
            }

            return self::FAILURE;
        }

        if ($result['rows'] === []) {
            $this->error('The file has no data rows.');

            return self::FAILURE;
        }

        $demoCount = Facility::query()->where('source', 'synthetic')->count();
        $removeDemo = (bool) $this->option('remove-demo-data');

        if ($dryRun) {
            $existing = collect($result['rows'])->filter(
                fn (array $row) => Facility::query()
                    ->where('name', $row['attributes']['name'])
                    ->where('county', $row['attributes']['county'])
                    ->exists()
            )->count();

            $total = count($result['rows']);
            $this->info("Dry run: {$total} rows are valid. Would create ".($total - $existing)." and update {$existing} facilities.");

            if ($removeDemo) {
                $this->info("Would delete {$demoCount} demo facilities.");
            }

            return self::SUCCESS;
        }

        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($result, $source, $removeDemo, &$created, &$updated): void {
            foreach ($result['rows'] as $row) {
                $facility = Facility::query()->updateOrCreate(
                    ['name' => $row['attributes']['name'], 'county' => $row['attributes']['county']],
                    array_merge($row['attributes'], ['source' => $source]),
                );

                $facility->services()->sync($row['service_ids']);

                $facility->wasRecentlyCreated ? $created++ : $updated++;
            }

            if ($removeDemo) {
                Facility::query()->where('source', 'synthetic')->delete();
            }
        });

        $this->info("Imported from \"{$source}\": created {$created}, updated {$updated} facilities.");

        if ($removeDemo) {
            $this->info("Deleted {$demoCount} demo facilities.");
        }

        return self::SUCCESS;
    }
}
