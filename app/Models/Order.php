<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_ref',
        'customer_name',
        'customer_email',
        'amount',
        'status',
    ];

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
