<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashDealProduct extends Model
{
    public function deal_products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
