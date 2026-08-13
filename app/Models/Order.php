<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'total_price',
        'status',
        'payment_method', // Add this line
        'payment_id', // Add this line
    ];


    protected $casts = [
        'total_price' => 'float',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeShipping($query)
    {
        return $query->where('status', 'shipping');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'delivered');
    }
}
