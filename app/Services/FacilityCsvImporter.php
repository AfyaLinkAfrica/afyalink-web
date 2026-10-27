<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

/**
 * Reads and validates a facility CSV file. It never writes to the database,
 * so the same checks serve both dry runs and real imports.
 */
class FacilityCsvImporter
{
    public const REQUIRED_COLUMNS = ['name', 'type', 'county'];

    public const COLUMNS = [
        'name', 'type', 'county', 'sub_county', 'address', 'phone',
        'latitude', 'longitude', 'services', 'verified_at',
    ];

    /**
     * Rough bounding box of Kenya. Widen it (and document why) when the project grows beyond Kenya.
     */
    private const LATITUDE_RANGE = [-5.0, 5.5];

    private const LONGITUDE_RANGE = [33.5, 42.0];

    /**
     * @return array{rows: array<int, array{line: int, attributes: array<string, mixed>, service_ids: array<int, int>}>, errors: array<int, string>}
     *
     * @throws InvalidArgumentException when the file cannot be read
     */
    public function read(string $path): array
    {
        $handle = @fopen($path, 'r');

        if ($handle === false) {
            throw new InvalidArgumentException("The file {$path} could not be opened.");
        }

        $errors = [];
        $rows = [];

        try {
            $header = fgetcsv($handle, 0, ',', '"', '\\');

            if ($header === false || $header === [null]) {
                throw new InvalidArgumentException('The file is empty.');
            }

            $header = array_map(fn ($column) => strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $column))), $header);

            foreach (array_diff($header, self::COLUMNS) as $unknown) {
                $errors[] = "Header: unknown column \"{$unknown}\". Allowed columns: ".implode(', ', self::COLUMNS).'.';
            }

            foreach (array_diff(self::REQUIRED_COLUMNS, $header) as $missing) {
                $errors[] = "Header: required column \"{$missing}\" is missing.";
            }

            if ($errors !== []) {
                return ['rows' => [], 'errors' => $errors];
            }

            $serviceIds = Service::query()->get()
                ->mapWithKeys(fn (Service $service) => [mb_strtolower($service->name) => $service->id]);

            $seen = [];
            $line = 1;

            while (($values = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                $line++;

                if ($values === [null]) {
                    continue; // blank line
                }

                if (count($values) !== count($header)) {
                    $errors[] = "Row {$line}: has ".count($values).' columns but the header has '.count($header).'.';

                    continue;
                }

                $data = array_combine($header, array_map(fn ($value) => trim((string) $value), $values));
                [$row, $problems] = $this->validateRow($line, $data, $serviceIds, $seen);

                if ($problems === []) {
                    $rows[] = $row;
                } else {
                    array_push($errors, ...$problems);
                }
            }
        } finally {
            fclose($handle);
        }

        return ['rows' => $errors === [] ? $rows : [], 'errors' => $errors];
    }

    /**
     * @param  array<string, string>  $data
     * @param  Collection<string, int>  $serviceIds
     * @param  array<string, int>  $seen
     * @return array{0: ?array{line: int, attributes: array<string, mixed>, service_ids: array<int, int>}, 1: array<int, string>}
     */
    private function validateRow(int $line, array $data, $serviceIds, array &$seen): array
    {
        $data = array_merge(array_fill_keys(self::COLUMNS, ''), $data);
        $data = array_map(fn ($value) => $value === '' ? null : $value, $data);

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Facility::TYPES)],
            'county' => ['required', Rule::in(config('afyalink.counties'))],
            'sub_county' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'latitude' => ['nullable', 'numeric', 'between:'.implode(',', self::LATITUDE_RANGE), 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:'.implode(',', self::LONGITUDE_RANGE), 'required_with:latitude'],
            'services' => ['nullable', 'string'],
            'verified_at' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        $problems = [];

        foreach ($validator->errors()->messages() as $field => $messages) {
            $problems[] = "Row {$line}: {$field}: ".implode(' ', $messages);
        }

        $ids = [];

        foreach (array_filter(array_map('trim', explode(';', (string) $data['services']))) as $serviceName) {
            $id = $serviceIds->get(mb_strtolower($serviceName));

            if ($id === null) {
                $problems[] = "Row {$line}: services: unknown service \"{$serviceName}\". Add it to the services list first.";
            } else {
                $ids[$id] = $id;
            }
        }

        if ($problems === [] && is_string($data['name']) && is_string($data['county'])) {
            $key = mb_strtolower($data['name'].'|'.$data['county']);

            if (isset($seen[$key])) {
                $problems[] = "Row {$line}: duplicate of row {$seen[$key]} (same name and county).";
            } else {
                $seen[$key] = $line;
            }
        }

        if ($problems !== []) {
            return [null, $problems];
        }

        $row = [
            'line' => $line,
            'attributes' => [
                'name' => $data['name'],
                'type' => $data['type'],
                'county' => $data['county'],
                'sub_county' => $data['sub_county'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'latitude' => $data['latitude'] === null ? null : (float) $data['latitude'],
                'longitude' => $data['longitude'] === null ? null : (float) $data['longitude'],
                'verified_at' => $data['verified_at'] === null ? null : Carbon::parse($data['verified_at']),
            ],
            'service_ids' => array_values($ids),
        ];

        return [$row, []];
    }
}
