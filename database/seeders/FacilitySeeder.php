<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Service;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Seed synthetic facilities only, each offering one to four services.
     */
    public function run(): void
    {
        $this->call(ServiceSeeder::class);

        $serviceIds = Service::query()->pluck('id');

        Facility::factory()->count(50)->create()->each(function (Facility $facility) use ($serviceIds): void {
            $facility->services()->attach($serviceIds->random(random_int(1, min(4, $serviceIds->count()))));
        });
    }
}
