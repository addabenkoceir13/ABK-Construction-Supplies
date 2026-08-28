<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DebtProduct>
 */
class DebtProductFactory extends Factory
{
    public function definition()
    {
        return [
            'debt_id' => Debt::factory(),
            'subcategory_id' => SubCategory::factory(),
            'name_category' => $this->faker->word(),
            'quantity' => (string) $this->faker->numberBetween(1, 20),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'date_debt' => $this->faker->date(),
            'status' => 0,
        ];
    }
}
