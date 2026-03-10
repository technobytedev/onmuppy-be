<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'vendor_id',
        'status',
        'quantity',
        'confirmed_at',
        'expires_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function isFresh(): bool
    {
        return $this->expires_at->isFuture();
    }

    public function isInStock(): bool
    {
        return $this->isFresh() && $this->status !== 'out_of_stock';
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeFresh($query)
    {
        return $query->where('expires_at', '>', now());
    }

    public function scopeInStock($query)
    {
        return $query->fresh()->where('status', '!=', 'out_of_stock');
    }
}
