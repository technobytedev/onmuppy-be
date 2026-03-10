<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'courier_id',
        'items',
        'total_amount',
        'delivery_fee',
        'status',
        'delivery_address',
        'delivery_lat',
        'delivery_lng',
        'notes',
        'accepted_at',
        'delivered_at',
    ];

    protected $casts = [
        'items'        => 'array',      // [{ product_id, qty, price }]
        'total_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'delivery_lat' => 'decimal:7',
        'delivery_lng' => 'decimal:7',
        'accepted_at'  => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['accepted', 'picked_up', 'in_transit']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'delivered');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function grandTotal(): float
    {
        return (float) $this->total_amount + (float) $this->delivery_fee;
    }

    public function isAssigned(): bool
    {
        return !is_null($this->courier_id);
    }
}
