<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $orderProducts = 'orderProducts';

    protected $fillable = ['order_id', 'product_id', 'product_quantity'];
}
