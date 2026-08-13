<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Contact extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'message'];
}
