<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $table = 'orderProducts';

    protected $fillable = ['order_id', 'product_id', 'product_price', 'product_quantity'];

}