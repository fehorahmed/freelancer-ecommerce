<?php

namespace Database\Seeders;

use App\Models\Warranty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarrantySeeder extends Seeder
{

    protected $warranties = [
        [ 'title' => '1 Year Warranty', 'description' => '1 Year Warranty' ],
        [ 'title' => '2 Year Warranty', 'description' => '2 Year Warranty' ],
        [ 'title' => '3 Year Warranty', 'description' => '3 Year Warranty' ],


    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->warranties as $index => $warranty) {
            $result = Warranty::create($warranty);
            if (!$result) {
                $this->command->info("Inserted at record $index.");
                return;
            }
            $this->command->info('Inserted '.count($this->warranties) . ' records');
        }
    }
}
