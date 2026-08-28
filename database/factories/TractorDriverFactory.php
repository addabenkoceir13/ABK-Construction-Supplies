<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TractorDriver>
 */
class TractorDriverFactory extends Factory
{
    public function definition()
    {
        return [
            'fullname' => $this->faker->name(),
            'phone' => $this->faker->numerify('06########'),
            'type' => 'delivery',
            'status' => 'active',
        ];
    }

    public function normal()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'normal',
        ]);
    }
}
