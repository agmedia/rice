<?php

namespace App\Models\Back\Catalog\Product;

use Illuminate\Database\Eloquent\Model;

class ProductPriceHistory extends Model
{
    protected $table = 'product_price_history';
    protected $guarded = [];
    protected $casts = [
        'effective_at' => 'datetime',
        'ended_at'     => 'datetime',
    ];
}