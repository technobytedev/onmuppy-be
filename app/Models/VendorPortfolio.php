<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPortfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'title',
        'description',
        'image',
        'type',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeWorkSamples($query)
    {
        return $query->where('type', 'work_sample');
    }

    public function scopeCertifications($query)
    {
        return $query->where('type', 'certification');
    }

    public function scopeBeforeAfter($query)
    {
        return $query->where('type', 'before_after');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}