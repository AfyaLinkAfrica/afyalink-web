<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Standard service names. Safe to run repeatedly: services are matched by slug.
     * Wording should be reviewed by a healthcare professional before real data is loaded.
     */
    public const NAMES = [
        'Outpatient care',
        'Maternity and delivery',
        'Antenatal and postnatal care',
        'Child immunisation',
        'Family planning',
        'HIV testing and counselling',
        'Laboratory services',
        'Pharmacy',
        'Dental care',
        'Eye care',
    ];

    public function run(): void
    {
        foreach (self::NAMES as $name) {
            Service::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
