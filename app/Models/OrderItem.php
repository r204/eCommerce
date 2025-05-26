<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];
    //
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
