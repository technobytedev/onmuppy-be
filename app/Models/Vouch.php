<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vouch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'neighborhood_id',
        'comment',
        'context',
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

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeFromNeighborhood($query, int $neighborhoodId)
    {
        return $query->where('neighborhood_id', $neighborhoodId);
    }

    public function scopeByContext($query, string $context)
    {
        return $query->where('context', $context);
    }
}
