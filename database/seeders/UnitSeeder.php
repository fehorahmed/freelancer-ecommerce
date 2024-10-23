<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('units')->insert([
            'name' => 'KG',
            'created_by' => 1

        ]);
        DB::table('units')->insert([
            'name' => 'Meter',
            'created_by' => 1

        ]);
    }
}
