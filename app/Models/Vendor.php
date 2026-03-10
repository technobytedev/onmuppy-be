<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'business_name',
        'slug',
        'description',
        'category',
        'phone',
        'avatar',
        'type',
        'latitude',
        'longitude',
        'address',
        'service_radius_km',
        'is_verified',
        'is_open',
        'neighborhood_id',
    ];

    protected $casts = [
        'latitude'          => 'decimal:7',
        'longitude'         => 'decimal:7',
        'service_radius_km' => 'decimal:2',
        'is_verified'       => 'boolean',
        'is_open'           => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function locations()
    {
        return $this->hasMany(VendorLocation::class);
    }

    public function currentLocation()
    {
        return $this->hasOne(VendorLocation::class)
                    ->where('expires_at', '>', now())
                    ->latestOfMany('broadcast_at');
    }

    public function flashAlerts()
    {
        return $this->hasMany(FlashAlert::class);
    }

    public function activeFlashAlerts()
    {
        return $this->hasMany(FlashAlert::class)
                    ->where('is_active', true)
                    ->where('expires_at', '>', now());
    }

    public function vouches()
    {
        return $this->hasMany(Vouch::class);
    }

    public function stockUpdates()
    {
        return $this->hasMany(StockUpdate::class);
    }

    public function deliveryRequests()
    {
        return $this->hasMany(DeliveryRequest::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function isMobile(): bool
    {
        return $this->type === 'mobile';
    }

    public function vouchCount(): int
    {
        return $this->vouches()->count();
    }

    public function neighborhoodVouchCount(?int $neighborhoodId): int
    {
        return $this->vouches()
                    ->where('neighborhood_id', $neighborhoodId)
                    ->count();
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function schedules()
    {
        return $this->hasMany(VendorSchedule::class);
    }

    public function portfolio()
    {
        return $this->hasMany(VendorPortfolio::class)->orderBy('sort_order');
    }

    public function isServiceProvider(): bool
    {
        return in_array($this->type, ['freelance', 'home_based']);
    }

    public function isProductSeller(): bool
    {
        return in_array($this->type, ['fixed', 'mobile']);
    }
}
