<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/categories.json');
        $categories = json_decode($json);

        foreach ($categories as $key => $value) {
            Category::create([
                'category_id' => $value->category_id,
                'category_title' => $value->category_title
            ]);
        }
    }
}
