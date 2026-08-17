<?php

namespace Database\Factories;

use App\Models\FuelStation;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FuelStation>
 */
class FuelStationFactory extends Factory
{
    protected $model = FuelStation::class;

    public function definition()
    {
        return [
            'vehicle_id'       => Vehicle::factory(),
            'name_owner'       => $this->faker->name(),
            'name_driver'      => $this->faker->name(),
            'name_distributor' => $this->faker->company(),
            'filing_datetime'  => now()->format('Y-m-d H:i:s'),
            'liter'            => 100.00,
            'amount'           => 5000.00,
            'status'           => 'unpaid',
            'type_fuel'        => 'diesel',
        ];
    }

    public function paid()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
        ]);
    }
}
