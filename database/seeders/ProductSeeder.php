<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/products.json');
        $products = json_decode($json);

        foreach ($products as $key => $value) {
            Product::create([
                'product_id' => $value->product_id,
                'title' => $value->title,
                'category_id' => $value->category_id,
                'category_title' => $value->category_title,
                'author' => $value->author,
                'list_price' => $value->list_price,
                'stock_quantity' => $value->stock_quantity,
            ]);
        }
    }
}
