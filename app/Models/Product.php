<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'name',
        'slug',
        'description',
        'price',
        'unit',
        'category',
        'image',
        'is_available',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'is_available' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function stockUpdates()
    {
        return $this->hasMany(StockUpdate::class);
    }

    public function latestStock()
    {
        return $this->hasOne(StockUpdate::class)
                    ->where('expires_at', '>', now())
                    ->latestOfMany('confirmed_at');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function isInStock(): bool
    {
        $stock = $this->latestStock;
        return $stock && $stock->status !== 'out_of_stock';
    }
}
