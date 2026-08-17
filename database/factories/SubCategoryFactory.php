<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubCategory>
 */
class SubCategoryFactory extends Factory
{
    protected $model = SubCategory::class;

    public function definition()
    {
        return [
            'category_id' => Category::factory(),
            'name'        => $this->faker->unique()->words(2, true),
            'input_type'  => 'number',
        ];
    }
}
