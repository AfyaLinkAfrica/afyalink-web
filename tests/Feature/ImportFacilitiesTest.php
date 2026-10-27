<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

class ImportFacilitiesTest extends TestCase
{
    use RefreshDatabase;

    private const HEADER = ['name', 'type', 'county', 'sub_county', 'address', 'phone', 'latitude', 'longitude', 'services', 'verified_at'];

    /** @var array<int, string> */
    private array $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    /**
     * @param  array<int, array<string, string>>  $rows
     * @param  array<int, string>|null  $header
     */
    private function csv(array $rows, ?array $header = null, string $prefix = ''): string
    {
        $header ??= self::HEADER;
        $path = sys_get_temp_dir().'/'.uniqid('facilities_', true).'.csv';
        $this->files[] = $path;

        $handle = fopen($path, 'w');
        fwrite($handle, $prefix);
        fputcsv($handle, $header, ',', '"', '\\');

        foreach ($rows as $row) {
            fputcsv($handle, array_map(fn (string $column) => $row[$column] ?? '', $header), ',', '"', '\\');
        }

        fclose($handle);

        return $path;
    }

    /**
     * @param  array<string, string>  $overrides
     * @return array<string, string>
     */
    private function row(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Import Test Clinic',
            'type' => 'clinic',
            'county' => 'Machakos',
            'sub_county' => 'Mavoko',
            'address' => 'Main Road',
            'phone' => '+254700000111',
            'latitude' => '-1.5',
            'longitude' => '37.2',
            'services' => 'Pharmacy',
            'verified_at' => '2026-10-01',
        ], $overrides);
    }

    private function runImport(string $file, array $options = []): PendingCommand
    {
        return $this->artisan('facilities:import', array_merge([
            'file' => $file,
            '--source' => 'Test Source',
            '--confirm-licence' => true,
        ], $options));
    }

    public function test_a_valid_file_creates_facilities_with_services_and_source(): void
    {
        $pharmacy = Service::factory()->create(['name' => 'Pharmacy', 'slug' => 'pharmacy']);
        $dental = Service::factory()->create(['name' => 'Dental care', 'slug' => 'dental-care']);

        $file = $this->csv([
            $this->row(['services' => 'pharmacy; Dental care']),
            $this->row(['name' => 'Second Clinic', 'services' => '']),
        ]);

        $this->runImport($file)->assertExitCode(0);

        $facility = Facility::where('name', 'Import Test Clinic')->firstOrFail();
        $this->assertSame('Test Source', $facility->source);
        $this->assertSame('Machakos', $facility->county);
        $this->assertEqualsCanonicalizing([$pharmacy->id, $dental->id], $facility->services->pluck('id')->all());
        $this->assertSame(0, Facility::where('name', 'Second Clinic')->firstOrFail()->services()->count());
        $this->assertSame(2, Facility::count());
    }

    public function test_the_verified_date_is_optional_and_empty_means_unverified(): void
    {
        $file = $this->csv([
            $this->row(['name' => 'Verified One', 'services' => '', 'verified_at' => '2026-10-01']),
            $this->row(['name' => 'Unverified One', 'services' => '', 'verified_at' => '']),
        ]);

        $this->runImport($file)->assertExitCode(0);

        $this->assertTrue(Facility::where('name', 'Verified One')->first()->isVerified());
        $this->assertFalse(Facility::where('name', 'Unverified One')->first()->isVerified());
    }

    public function test_a_dry_run_reports_but_writes_nothing(): void
    {
        $file = $this->csv([$this->row(['services' => ''])]);

        $this->artisan('facilities:import', ['file' => $file, '--source' => 'Test Source', '--dry-run' => true])
            ->expectsOutputToContain('Would create 1')
            ->assertExitCode(0);

        $this->assertSame(0, Facility::count());
    }

    public function test_an_existing_facility_is_updated_instead_of_duplicated(): void
    {
        Facility::factory()->create(['name' => 'Import Test Clinic', 'county' => 'Machakos', 'phone' => '+254700999999']);

        $file = $this->csv([$this->row(['services' => '', 'phone' => '+254700000111'])]);

        $this->runImport($file)->assertExitCode(0);

        $this->assertSame(1, Facility::count());
        $this->assertSame('+254700000111', Facility::first()->phone);
        $this->assertSame('Test Source', Facility::first()->source);
    }

    public function test_a_source_name_is_required(): void
    {
        $file = $this->csv([$this->row(['services' => ''])]);

        $this->artisan('facilities:import', ['file' => $file, '--confirm-licence' => true])->assertExitCode(1);

        $this->assertSame(0, Facility::count());
    }

    public function test_the_synthetic_source_name_is_reserved(): void
    {
        $file = $this->csv([$this->row(['services' => ''])]);

        $this->runImport($file, ['--source' => 'Synthetic'])->assertExitCode(1);

        $this->assertSame(0, Facility::count());
    }

    public function test_a_real_import_needs_the_licence_confirmation(): void
    {
        $file = $this->csv([$this->row(['services' => ''])]);

        $this->artisan('facilities:import', ['file' => $file, '--source' => 'Test Source'])->assertExitCode(1);

        $this->assertSame(0, Facility::count());
    }

    public function test_a_missing_file_fails_cleanly(): void
    {
        $this->runImport('/no/such/file.csv')->assertExitCode(1);
    }

    public function test_invalid_rows_stop_the_whole_import(): void
    {
        $file = $this->csv([
            $this->row(['name' => 'Good Row', 'services' => '']),
            $this->row(['name' => 'Bad County', 'county' => 'Atlantis', 'services' => '']),
            $this->row(['name' => 'Bad Type', 'type' => 'spaceship', 'services' => '']),
        ]);

        $this->runImport($file)
            ->expectsOutputToContain('Row 3')
            ->expectsOutputToContain('Row 4')
            ->assertExitCode(1);

        $this->assertSame(0, Facility::count());
    }

    public function test_coordinates_must_be_inside_kenya_and_come_as_a_pair(): void
    {
        $file = $this->csv([
            $this->row(['name' => 'Far Away', 'latitude' => '48.8', 'longitude' => '2.3', 'services' => '']),
            $this->row(['name' => 'Half A Pair', 'latitude' => '-1.5', 'longitude' => '', 'services' => '']),
        ]);

        $this->runImport($file)
            ->expectsOutputToContain('Row 2')
            ->expectsOutputToContain('Row 3')
            ->assertExitCode(1);

        $this->assertSame(0, Facility::count());
    }

    public function test_unknown_service_names_are_reported(): void
    {
        $file = $this->csv([$this->row(['services' => 'Time travel'])]);

        $this->runImport($file)
            ->expectsOutputToContain('unknown service "Time travel"')
            ->assertExitCode(1);

        $this->assertSame(0, Facility::count());
    }

    public function test_a_missing_required_column_is_reported(): void
    {
        $file = $this->csv([['name' => 'No County', 'type' => 'clinic']], ['name', 'type']);

        $this->runImport($file)
            ->expectsOutputToContain('required column "county" is missing')
            ->assertExitCode(1);
    }

    public function test_an_unknown_column_is_reported(): void
    {
        $file = $this->csv([$this->row(['services' => ''])], [...self::HEADER, 'nickname']);

        $this->runImport($file)
            ->expectsOutputToContain('unknown column "nickname"')
            ->assertExitCode(1);
    }

    public function test_duplicate_rows_in_the_file_are_reported(): void
    {
        $file = $this->csv([
            $this->row(['services' => '']),
            $this->row(['services' => '']),
        ]);

        $this->runImport($file)
            ->expectsOutputToContain('duplicate of row 2')
            ->assertExitCode(1);

        $this->assertSame(0, Facility::count());
    }

    public function test_a_utf8_byte_order_mark_in_the_header_is_handled(): void
    {
        $file = $this->csv([$this->row(['services' => ''])], null, "\xEF\xBB\xBF");

        $this->runImport($file)->assertExitCode(0);

        $this->assertSame(1, Facility::count());
    }

    public function test_blank_lines_are_ignored(): void
    {
        $file = $this->csv([$this->row(['services' => ''])]);
        file_put_contents($file, "\n", FILE_APPEND);

        $this->runImport($file)->assertExitCode(0);

        $this->assertSame(1, Facility::count());
    }

    public function test_demo_data_is_removed_only_after_a_successful_import(): void
    {
        Facility::factory()->create(['name' => 'Demo Clinic', 'source' => 'synthetic']);

        $bad = $this->csv([$this->row(['county' => 'Atlantis', 'services' => ''])]);
        $this->runImport($bad, ['--remove-demo-data' => true])->assertExitCode(1);
        $this->assertSame(1, Facility::where('source', 'synthetic')->count());

        $good = $this->csv([$this->row(['services' => ''])]);
        $this->runImport($good, ['--remove-demo-data' => true])->assertExitCode(0);

        $this->assertSame(0, Facility::where('source', 'synthetic')->count());
        $this->assertSame(1, Facility::where('source', 'Test Source')->count());
    }

    public function test_the_demo_notice_disappears_once_only_real_data_remains(): void
    {
        Facility::factory()->create(['name' => 'Demo Clinic', 'source' => 'synthetic']);

        $file = $this->csv([$this->row(['name' => 'Real Clinic', 'services' => ''])]);
        $this->runImport($file, ['--remove-demo-data' => true])->assertExitCode(0);

        $this->get(route('facilities.index'))
            ->assertSee('Real Clinic')
            ->assertDontSee('Demo Clinic')
            ->assertDontSee(__('facilities.demo_notice'));
    }
}
