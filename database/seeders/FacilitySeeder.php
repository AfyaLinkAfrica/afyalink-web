<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Seed synthetic facilities only.
     */
    public function run(): void
    {
        Facility::factory()->count(50)->create();
    }
}
