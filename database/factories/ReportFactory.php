<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'facility_id' => Facility::factory(),
            'message' => 'Sample report: the phone number has changed.',
            'contact' => null,
            'status' => Report::STATUS_NEW,
        ];
    }

    public function resolved(): static
    {
        return $this->state(fn () => ['status' => Report::STATUS_RESOLVED]);
    }
}
