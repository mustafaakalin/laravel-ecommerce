<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id');
    }
}
