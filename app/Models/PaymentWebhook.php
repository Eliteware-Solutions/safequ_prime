<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWebhook extends Model
{
    protected $guarded = [];
    protected $fillable = ['type', 'webhook_data'];
}
