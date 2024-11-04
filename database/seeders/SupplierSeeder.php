<?php

namespace Database\Seeders;

use App\Models\TractorDriver;
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
        TractorDriver::create([
          'fullname' => 'supplier',
          'phone'    => '0555555555',
          'status'   => 'active',
        ]);

        TractorDriver::create([
          'fullname' => 'بن لادن',
          'phone'    => '0544332211',
          'status'   => 'active',
        ]);
    }
}
