<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Supplier::create([
          'fullname' => 'supplier',
          'phone'    => '0555555555',
          'status'   => 'active',
        ]);

        Supplier::create([
          'fullname' => 'Ben ladel',
          'phone'    => '0544332211',
          'status'   => 'active',
        ]);
    }
}
