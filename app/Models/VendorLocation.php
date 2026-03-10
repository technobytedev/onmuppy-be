<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'latitude',
        'longitude',
        'note',
        'broadcast_at',
        'expires_at',
    ];

    protected $casts = [
        'latitude'     => 'decimal:7',
        'longitude'    => 'decimal:7',
        'broadcast_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->expires_at->isFuture();
    }
}
