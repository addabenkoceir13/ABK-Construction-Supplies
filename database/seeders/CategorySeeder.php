<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
          'name' => 'بريك 10'
        ]);

        Category::create([
          'name' => 'بريك 12'
        ]);

        Category::create([
          'name' => 'لوردي'
        ]);

        Category::create([
          'name' => 'إسمنت الشلف'
        ]);

        Category::create([
          'name' => 'قرافي 15/25'
        ]);
    }
}
