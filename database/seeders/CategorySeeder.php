<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the top-level category
        $category = Category::create(['name' => 'Motherboard', 'slug' => 'motherboard']);

        // Create the subcategory AMD under Motherboard
        $sub_category = Category::create(['name' => 'AMD', 'slug' => 'amd', 'parent_id' => $category->id]);

        // Create the sub-subcategory AMD 500 Series under AMD
        $sub_category1 = Category::create(['name' => 'AMD 500 Series', 'slug' => 'amd_500_series', 'parent_id' => $sub_category->id]);

        // Create the sub-sub-subcategory ASUS under AMD 500 Series
        $sub_category2 = Category::create(['name' => 'ASUS', 'slug' => 'asus', 'parent_id' => $sub_category1->id]);
    }
}
