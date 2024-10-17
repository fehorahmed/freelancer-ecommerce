<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Super User',
            'email' => 'admin@gmail.com',
            'phone' => '01711223344',
            'password' => Hash::make('password'),
            'role' => 3,
        ]);
        DB::table('users')->insert([
            'name' => 'Manager',
            'email' => 'manager@gmail.com',
            'phone' => '01711223342',
            'password' => Hash::make('password'),
            'role' => 2,
        ]);
    }
}
