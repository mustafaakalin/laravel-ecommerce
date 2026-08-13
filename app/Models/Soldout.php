<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;

class Soldout extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'is_sold',
        'notes'
    ];

    protected $casts = [
        'is_sold' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order_items()
    {
        return $this->hasOne(OrderItem::class, 'order_id', 'order_id')
            ->where('product_id', $this->product_id);
    }
}
