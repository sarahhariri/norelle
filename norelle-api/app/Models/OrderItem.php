<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
#[Fillable(  'product_variant_id', 'product_name', 'size',
    'unit_price', 'quantity', 'line_total')]
class OrderItem extends Model
{
    public function order()
{
    return $this->belongsTo(Order::class);
}
}
