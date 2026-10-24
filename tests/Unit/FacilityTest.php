<?php

namespace Tests\Unit;

use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_creates_a_synthetic_facility(): void
    {
        $facility = Facility::factory()->create();

        $this->assertDatabaseHas('facilities', ['id' => $facility->id]);
        $this->assertSame('synthetic', $facility->source);
        $this->assertContains($facility->type, Facility::TYPES);
    }

    public function test_verified_and_unverified_states(): void
    {
        $this->assertTrue(Facility::factory()->verified()->create()->isVerified());
        $this->assertFalse(Facility::factory()->unverified()->create()->isVerified());
    }

    public function test_facility_names_are_unique_in_a_seeded_batch(): void
    {
        Facility::factory()->count(20)->create();

        $this->assertSame(20, Facility::query()->distinct('name')->count('name'));
    }
}
