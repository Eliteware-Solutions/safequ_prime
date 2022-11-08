<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $fillable = ['product_id', 'qty', 'price', 'seller_id', 'est_shipping_days', 'purchase_start_date', 'purchase_end_date','is_best_selling'];

    //
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function wholesalePrices()
    {
        return $this->hasMany(WholesalePrice::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, OrderDetail::class, 'product_stock_id', 'id', 'id', 'order_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class)->where('payment_status', 'paid');
    }
}
