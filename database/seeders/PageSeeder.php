<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pages')->insert([
            'title' => 'About Us',
            'url' => 'about-us',
            'text' => 'description',
            'image' => null,
            'created_by' => 1
        ]);
        DB::table('pages')->insert([
            'title' => 'Terms and conditions',
            'url' => 'terms-and-conditions',
            'text' => 'description',
            'image' => null,
            'created_by' => 1
        ]);
    }
}
