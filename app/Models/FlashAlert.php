<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'title',
        'message',
        'discount_percent',
        'original_price',
        'flash_price',
        'alert_radius_km',
        'latitude',
        'longitude',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'original_price'   => 'decimal:2',
        'flash_price'      => 'decimal:2',
        'alert_radius_km'  => 'decimal:2',
        'latitude'         => 'decimal:7',
        'longitude'        => 'decimal:7',
        'starts_at'        => 'datetime',
        'expires_at'       => 'datetime',
        'is_active'        => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('starts_at', '<=', now())
                     ->where('expires_at', '>', now());
    }

    /**
     * Filter flash alerts within X km of a given coordinate.
     * Uses Haversine formula approximation via raw SQL.
     */
    public function scopeNearby($query, float $lat, float $lng)
    {
        return $query->whereRaw("
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )) <= alert_radius_km
        ", [$lat, $lng, $lat]);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function isLive(): bool
    {
        return $this->is_active
            && $this->starts_at->isPast()
            && $this->expires_at->isFuture();
    }
}
