<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\DebtHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DebtHistory>
 */
class DebtHistoryFactory extends Factory
{
    protected $model = DebtHistory::class;

    public function definition()
    {
        return [
            'debt_id' => Debt::factory(),
            // NOTE: debt_histories.amount is decimal(8,2), i.e. max 999999.99,
            // while debts.total_debt_amount is decimal(20,2). Keep test amounts
            // small enough to fit the narrower history column.
            'amount'  => 250.00,
            'date'    => now()->format('Y-m-d H:i:s'),
        ];
    }
}
