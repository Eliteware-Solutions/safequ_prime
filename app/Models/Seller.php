<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{

    protected $with = ['user', 'user.shop'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function seller_package()
    {
        return $this->belongsTo(SellerPackage::class);
    }

    public function products_purchase_started()
    {
        return $this->hasMany(ProductStock::class)->with('product')->where('purchase_start_date', '<=', date('Y-m-d H:i:s'))->where('purchase_end_date', '>=', date('Y-m-d H:i:s'));
    }

    public function products_purchase_expired()
    {
        return $this->hasMany(ProductStock::class)->with('product')->where('purchase_end_date', '<=', date('Y-m-d H:i:s'))->whereDate('purchase_end_date', date('Y-m-d'));
    }
}
