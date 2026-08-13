<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'title', 'first_name', 'last_name', 'phone',
        'address', 'city', 'state', 'country', 'zip_code', 'is_default','company_name',
        'tax_number', 'tax_office'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
