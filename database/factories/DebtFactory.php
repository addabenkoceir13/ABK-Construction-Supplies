<?php

namespace Database\Factories;

use App\Models\TractorDriver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Debt>
 */
class DebtFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'tractor_driver_id' => TractorDriver::factory(),
            'fullname' => $this->faker->name(),
            'phone' => $this->faker->numerify('06########'),
            'date_debut_debt' => $this->faker->date(),
            'total_debt_amount' => 0,
            'debt_paid' => 0,
            'rest_debt_amount' => 0,
            'date_end_debt' => null,
            'status' => 'unpaid',
            'note' => null,
        ];
    }
}
