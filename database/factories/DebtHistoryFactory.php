<?php

namespace Database\Factories;

use App\Models\Debt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DebtHistory>
 */
class DebtHistoryFactory extends Factory
{
    public function definition()
    {
        return [
            'debt_id' => Debt::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'date' => $this->faker->dateTime(),
        ];
    }
}
