<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'latitude',
        'longitude',
        'neighborhood',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'latitude'          => 'decimal:7',
            'longitude'         => 'decimal:7',
            'is_active'         => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function neighborhoods()
    {
        return $this->belongsToMany(Neighborhood::class, 'user_neighborhoods')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    public function primaryNeighborhood()
    {
        return $this->belongsToMany(Neighborhood::class, 'user_neighborhoods')
                    ->wherePivot('is_primary', true)
                    ->withTimestamps();
    }

    public function vouches()
    {
        return $this->hasMany(Vouch::class);
    }

    public function deliveryRequests()
    {
        return $this->hasMany(DeliveryRequest::class);
    }

    public function courierDeliveries()
    {
        return $this->hasMany(DeliveryRequest::class, 'courier_id');
    }
    
    // ─── Helpers ──────────────────────────────────────────────────

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isCourier(): bool
    {
        return $this->role === 'courier';
    }

    public function isServiceProvider(): bool
    {
        return $this->role === 'vendor'
            && $this->vendor?->type === 'freelance';
    }

    public function isProductSeller(): bool
    {
        return $this->role === 'vendor'
            && in_array($this->vendor?->type, ['fixed', 'mobile', 'home_based']);
    }
}
