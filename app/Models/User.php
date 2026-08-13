<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Panel;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;
    use \Spatie\Permission\Traits\HasRoles; // Bu trait'i ekleyin

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'surname',
        'identity_number',
        'avatar',
        'instagram_account',
        'facebook_account',
        'tiktok_account',
        'x_account',
        'phone',
        'github_id', // github ID
        'google_id', // Google ID
        'facebook_id', // facebook ID
        'instagram_id', // instagram ID
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->name.' '.$this->surname) ?: $this->name;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }


    public function likes()
    {
        return $this->hasMany(Like::class);
    }


    public function ratings()
    {
        return $this->hasMany(ProductRating::class);
    }


    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    


    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('admin', $panel->getAuthGuard());
    }

}
