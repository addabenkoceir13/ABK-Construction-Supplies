<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\DebtProduct;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DebtProduct>
 */
class DebtProductFactory extends Factory
{
    protected $model = DebtProduct::class;

    public function definition()
    {
        return [
            'debt_id'        => Debt::factory(),
            'subcategory_id' => SubCategory::factory(),
            'name_category'  => $this->faker->words(2, true),
            // Column is a string in the migration, not a numeric type.
            'quantity'       => '2',
            'amount'         => 500.00,
            'date_debt'      => now()->format('Y-m-d'),
            // enum('1','0') - MySQL treats an *integer* 0 as an enum index, not a
            // value, which truncates under strict mode. Must be the string '0'.
            'status'         => '0',
        ];
    }
}
