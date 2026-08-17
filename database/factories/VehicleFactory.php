<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition()
    {
        return [
            'name'          => $this->faker->word(),
            // enum('car','truck','motorcycle') - no default in the migration.
            'type'          => 'truck',
            'license_plate' => $this->faker->numberBetween(10000, 99999)
                . ' - ' . $this->faker->numberBetween(1990, 2024)
                . ' - ' . $this->faker->numberBetween(1, 48),
        ];
    }
}
