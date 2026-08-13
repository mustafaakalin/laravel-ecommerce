<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasPermissions;

class SliderForHomepage extends Model
{
    /** @use HasFactory<\Database\Factories\SliderForHomepageFactory> */
    use HasFactory;
    use HasPermissions;

    protected $fillable = [
        'title',
        'description',
        'image',
        'button_text',
        'button_link',
        'position',
        'link',
        'status',
    ];
}
