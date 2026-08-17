<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\TractorDriver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Debt>
 */
class DebtFactory extends Factory
{
    protected $model = Debt::class;

    public function definition()
    {
        return [
            'user_id'           => User::factory(),
            'tractor_driver_id' => TractorDriver::factory(),
            'fullname'          => $this->faker->name(),
            'phone'             => (string) $this->faker->numberBetween(600000000, 799999999),
            'date_debut_debt'   => now()->format('Y-m-d'),
            'total_debt_amount' => 1000.00,
            'debt_paid'         => 0.00,
            'rest_debt_amount'  => 1000.00,
            'date_end_debt'     => null,
            'status'            => 'unpaid',
            'note'              => null,
        ];
    }

    public function paid()
    {
        return $this->state(fn (array $attributes) => [
            'status'           => 'paid',
            'debt_paid'        => $attributes['total_debt_amount'] ?? 1000.00,
            'rest_debt_amount' => 0.00,
            'date_end_debt'    => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Amounts chosen so that a partial payment leaves a remainder.
     */
    public function withTotal(float $total, float $paid = 0.00)
    {
        return $this->state(fn (array $attributes) => [
            'total_debt_amount' => $total,
            'debt_paid'         => $paid,
            'rest_debt_amount'  => $total - $paid,
        ]);
    }
}
