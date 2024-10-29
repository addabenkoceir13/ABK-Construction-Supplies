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
          //  !! 1
          'name' => 'بريك 10'
        ]);

        Category::create([
          //  !! 2
          'name' => 'بريك 12'
        ]);

        Category::create([
          //  !! 3
          'name' => 'لوردي'
        ]);

        Category::create([
          //  !! 4
          'name' => 'إسمنت الشلف'
        ]);
        Category::create([
          //  !! 5
          'name' => 'إسمنت متين'
        ]);

        Category::create([
          //  !! 6
          'name' => 'قرافي 15/25 برويطة '
        ]);

        Category::create([
          //  !! 7
          'name' => 'قرافي 15/25 ريموك'
        ]);

        Category::create([
          //  !! 8
          'name' => 'قرافي 08/15 برويطة'
        ]);

        Category::create([
          //  !! 9
          'name' => 'قرافي 08/15 ريموك'
        ]);

        Category::create([
          //  !! 10
          'name' => 'قرافي 03/08 برويطة'
        ]);

        Category::create([
          //  !! 11
          'name' => 'قرافي 03/08 ريموك'
        ]);

        Category::create([
          //  !! 12
          'name' => 'صابل كريار برويطة'
        ]);

        Category::create([
          //  !! 13
          'name' => 'صابل كريار ريموك'
        ]);

        Category::create([
          //  !! 14
          'name' => 'رملة برويطة'
        ]);

        Category::create([
          //  !! 15
          'name' => 'رملة ريموك'
        ]);
        Category::create([
          //  !! 16
          'name' => 'الماء'
        ]);
    }
}
