<?php

namespace Database\Factories;

use App\Models\InsuranceVehicle;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InsuranceVehicle>
 */
class InsuranceVehicleFactory extends Factory
{
    protected $model = InsuranceVehicle::class;

    public function definition()
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'start_date' => now()->subMonths(6)->format('Y-m-d'),
            'end_date'   => now()->addMonths(6)->format('Y-m-d'),
        ];
    }

    public function expired()
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->subYears(2)->format('Y-m-d'),
            'end_date'   => now()->subDay()->format('Y-m-d'),
        ]);
    }
}
