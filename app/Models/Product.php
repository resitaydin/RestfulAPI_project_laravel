<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $products = 'products';

    protected $fillable = [
        'product_id' ,'title', 'category_id',
        'category_title', 'author', 'list_price',
        'stock_quantity'
    ];
}
