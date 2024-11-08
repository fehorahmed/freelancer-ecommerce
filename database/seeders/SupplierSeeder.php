<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('suppliers')->insert([
            'name' => 'Supplier 1',
            'phone' => '01934125665',
            'description' => 'description',
            'status' => 1,
            'created_by' => 1

        ]);
        DB::table('suppliers')->insert([
            'name' => 'Supplier 2',
            'phone' => '01750637286',
            'status' => 1,
            'created_by' => 1

        ]);
    }
}
