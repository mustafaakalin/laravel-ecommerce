<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\CartItemResource\Pages;
use App\Models\CartItem;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Testimonial extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'author', 'position', 'content', 'avatar', 'rating', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'float'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->useDisk('public');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
