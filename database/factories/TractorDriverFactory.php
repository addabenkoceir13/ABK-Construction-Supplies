<?php

namespace Database\Factories;

use App\Models\TractorDriver;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TractorDriver>
 */
class TractorDriverFactory extends Factory
{
    protected $model = TractorDriver::class;

    public function definition()
    {
        return [
            'fullname' => $this->faker->name(),
            'phone'    => (string) $this->faker->numberBetween(600000000, 799999999),
            'type'     => 'delivery',
            'status'   => 'active',
        ];
    }

    /**
     * The "normal" (walk-in customer) driver.
     *
     * NOTE: DebtRepository::debtUnPaid()/debtPaid() hardcode
     * whereTractorDriverId(1), so the debt listing pages only ever show rows
     * whose tractor_driver_id is literally 1. Tests that need the debt index
     * to render rows must create this record first so it lands on id 1.
     */
    public function normal()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'normal',
        ]);
    }

    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
