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
//        return $this->hasMany(ProductStock::class)->with('product')->where('purchase_start_date', '<=', date('Y-m-d H:i:s'))->where('purchase_end_date', '>=', date('Y-m-d H:i:s'))->orWhere('seller_id','=',0)->orderBy('purchase_end_date');
        return $this->hasMany(ProductStock::class)->with('product')->orWhere('seller_id','=',0)->orderBy('updated_at');
    }

    public function products_purchase_expired()
    {
        return $this->hasMany(ProductStock::class)->with('product')->where('purchase_end_date', '<=', date('Y-m-d H:i:s'))->whereDate('purchase_end_date', date('Y-m-d'));
    }

    public function best_selling_products_purchase_started()
    {
        return $this->hasMany(ProductStock::class)->with('product')->where('is_best_selling', 1)->where('purchase_start_date', '<=', date('Y-m-d H:i:s'))->where('purchase_end_date', '>=', date('Y-m-d H:i:s'))->orderBy('purchase_end_date');
    }
}
