<?php

namespace Database\Seeders;

use App\Models\SubCategory as ModelsSubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubCategory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ModelsSubCategory::create([
          'category_id' => 1,
          'name' => 'حبة',
          'input_type' => 'number',
        ]);

        ModelsSubCategory::create([
          'category_id' => 2,
          'name' => 'حبة',
          'input_type' => 'number',
        ]);

        ModelsSubCategory::create([
          'category_id' => 3,
          'name' => 'حبة',
          'input_type' => 'number',
        ]);

        ModelsSubCategory::create([
          'category_id' => 4,
          'name' => 'قنطار',
          'input_type' => 'number',
        ]);

        ModelsSubCategory::create([
          'category_id' => 5,
          'name' => 'قنطار',
          'input_type' => 'number',
        ]);

        ModelsSubCategory::create([
          'category_id' => 6,
          'name' => 'برويطة',
          'input_type' => 'number',
        ]);

        ModelsSubCategory::create([
          'category_id' => 7,
          'name' => '1/4',
          'input_type' => 'select',
        ]);
        ModelsSubCategory::create([
          'category_id' => 7,
          'name' => '2/4',
          'input_type' => 'select',
        ]);
        ModelsSubCategory::create([
          'category_id' => 7,
          'name' => '3/4',
          'input_type' => 'select',
        ]);
        ModelsSubCategory::create([
          'category_id' => 7,
          'name' => '4/4 = 1',
          'input_type' => 'select',
        ]);

        ModelsSubCategory::create([
          'category_id' => 8,
          'name' => 'برويطة',
          'input_type' => 'number',
        ]);

        ModelsSubCategory::create([
          'category_id' => 9,
          'name' => '1/4',
          'input_type' => 'select',
        ]);
        ModelsSubCategory::create([
          'category_id' => 9,
          'name' => '2/4',
          'input_type' => 'select',
        ]);
        ModelsSubCategory::create([
          'category_id' => 9,
          'name' => '3/4',
          'input_type' => 'select',
        ]);
        ModelsSubCategory::create([
          'category_id' => 9,
          'name' => '4/4 = 1',
          'input_type' => 'select',
        ]);

        ModelsSubCategory::create([
          'category_id' => 10,
          'name' => 'برويطة',
          'input_type' => 'number',
        ]);

        ModelsSubCategory::create([
          'category_id' => 11,
          'name' => '1/4',
          'input_type' => 'select',
        ]);
        ModelsSubCategory::create([
          'category_id' => 11,
          'name' => '2/4',
          'input_type' => 'select',
        ]);
        ModelsSubCategory::create([
          'category_id' => 11,
          'name' => '3/4',
          'input_type' => 'select',
        ]);
        ModelsSubCategory::create([
          'category_id' => 11,
          'name' => '4/4 = 1',
          'input_type' => 'select',
        ]);

        ModelsSubCategory::create([
          'category_id' => 12,
          'name' => 'برويطة',
          'input_type' => 'number',
        ]);

        ModelsSubCategory::create([
          'category_id' => 13,
          'name' => '1/4',
          'input_type' => 'select',
        ]);
        ModelsSubCategory::create([
          'category_id' => 13,
          'name' => '2/4',
          'input_type' => 'select',
        ]);
        ModelsSubCategory::create([
          'category_id' => 13,
          'name' => '3/4',
          'input_type' => 'select',
        ]);
        ModelsSubCategory::create([
          'category_id' => 13,
          'name' => '4/4 = 1',
          'input_type' => 'select',
        ]);

        ModelsSubCategory::create([
          'category_id' => 14,
          'name' => 'برويطة',
          'input_type' => 'number',
        ]);

        ModelsSubCategory::create([
          'category_id' => 15,
          'name' => '1/4',
          'input_type' => 'select',
        ]);
        ModelsSubCategory::create([
          'category_id' => 15,
          'name' => '2/4',
          'input_type' => 'select',
        ]);
        ModelsSubCategory::create([
          'category_id' => 15,
          'name' => '3/4',
          'input_type' => 'select',
        ]);
        ModelsSubCategory::create([
          'category_id' => 15,
          'name' => '4/4 = 1',
          'input_type' => 'select',
        ]);
    }
}
