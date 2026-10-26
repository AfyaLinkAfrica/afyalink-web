<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $name = 'Sample service '.$this->faker->unique()->numberBetween(1, 9999);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
