<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $table = 'campaigns';
    
    protected $fillable = ['campaign_name', 'conditions', 'discount_type',
    'max_condition','min_condition', 'gift_condition'
    ];

    protected $casts = [
        'conditions' => 'array',
    ];
}