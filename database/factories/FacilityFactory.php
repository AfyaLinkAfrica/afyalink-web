<?php

namespace Database\Factories;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Generates clearly FICTIONAL facilities. Never put real facility or patient data here.
 *
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    protected $model = Facility::class;

    private const COUNTIES = [
        'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Machakos', 'Kiambu',
        'Uasin Gishu', 'Kakamega', 'Meru', 'Nyeri', 'Kitui', 'Turkana',
    ];

    public function definition(): array
    {
        $type = $this->faker->randomElement(Facility::TYPES);
        $label = ucwords(str_replace('_', ' ', $type));

        return [
            'name' => 'Sample '.$label.' '.$this->faker->unique()->numberBetween(1, 9999),
            'type' => $type,
            'county' => $this->faker->randomElement(self::COUNTIES),
            'sub_county' => 'Sample Sub-County '.$this->faker->numberBetween(1, 20),
            'address' => 'P.O. Box '.$this->faker->numberBetween(1, 999).' (synthetic)',
            'phone' => '+254700'.$this->faker->numerify('######'),
            'latitude' => $this->faker->randomFloat(7, -4.6, 4.6),
            'longitude' => $this->faker->randomFloat(7, 34.0, 41.8),
            'source' => 'synthetic',
            'verified_at' => $this->faker->boolean(30) ? now() : null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => ['verified_at' => now()]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['verified_at' => null]);
    }
}
