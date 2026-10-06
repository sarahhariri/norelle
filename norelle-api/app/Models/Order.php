<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
#[Fillable(   'customer_name', 'phone', 'city', 'address', 'notes', 'subtotal', 'payment_method', 'status')]
class Order extends Model
{
    public function items()
{
    return $this->hasMany(OrderItem::class);
}
}
