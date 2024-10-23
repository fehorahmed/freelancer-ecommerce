<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{

    protected $categories = [
        [ 'id' => 1,'root_id' =>null, 'name' => 'Root Category', 'url' => 'root-category' ],
        [ 'id' => 2,'root_id' =>1,'name' => 'Smart Watch', 'url' => 'smart-watch' ],
        [ 'id' => 3,'root_id' =>1,'name' => 'Headphone', 'url' => 'headphone'],
        [ 'id' => 4,'root_id' =>1,'name' => 'Smart TV', 'url' => 'smart-tv'],
        [ 'id' => 5,'root_id' =>1,'name' => 'Life Style', 'url' => 'life-style'],
        [ 'id' => 6,'root_id' =>1,'name' => 'Luggage & Bags', 'url' => 'luggage-and-bags'],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->categories as $index => $category) {
            $result = Category::create($category);
            if (!$result) {
                $this->command->info("Inserted at record $index.");
                return;
            }
            $this->command->info('Inserted '.count($this->categories) . ' records');
        }
    }
}
